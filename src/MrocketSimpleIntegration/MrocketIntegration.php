<?php

namespace Aishmurodov\MrocketSimpleIntegration;

use Aishmurodov\MrocketSimpleIntegration\Helpers\Config;
use Aishmurodov\MrocketSimpleIntegration\Helpers\PhoneNormalizer;
use Aishmurodov\MrocketSimpleIntegration\Interfaces\ConfigInterface;
use Aishmurodov\MrocketSimpleIntegration\Interfaces\DebuggerInterface;
use Aishmurodov\MrocketSimpleIntegration\Interfaces\PhoneNormalizerInterface;
use AmoCRM\AmoAPI;
use AmoCRM\AmoAPIException;
use AmoCRM\AmoContact;
use AmoCRM\AmoLead;
use AmoCRM\TokenStorage\FileStorage;
use AmoCRM\TokenStorage\TokenStorageInterface;
use Exception;

class MrocketIntegration {

    private TokenStorageInterface $store;

    public static DebuggerInterface $debugger;

    public static PhoneNormalizerInterface $phoneNormalizer;

    public static ConfigInterface $config;

    public function __construct (string $defaultStorePath = __DIR__ . "/store") {
        $this->store = new FileStorage($defaultStorePath);

        if (empty(self::$debugger)) {
            self::$debugger = new Debugger();
        }

        if (empty(self::$phoneNormalizer)) {
            self::$phoneNormalizer = new PhoneNormalizer();
        }

        if (empty(self::$config)) {
            self::$config = new Config();
        }
    }

    public function isInvalidStateForAuth (): bool
    {
        return !isset($_GET['code'])
            || !isset($_GET['referer'])
            || !isset($_GET['client_id'])
            || !isset($_GET['state'])

            || explode(".", $_GET['referer'])[0] != self::$config->widget()->getSubDomain()
            || $_GET['client_id'] != self::$config->widget()->getId()
            || $_GET['state'] != self::$config->widget()->getState();
    }

    public function getLocation(): string
    {
        return "https://www.amocrm.ru/oauth?client_id=". self::$config->widget()->getId() ."&state=". self::$config->widget()->getState() ."&mode=post_message";
    }

    public function locateToAmo (): void
    {
        header("location: " . $this->getLocation());
        exit();
    }

    public function auth (string $code = "") {
        try {

            AmoAPI::$tokenStorage = $this->store;

            if (strlen($code) > 0) {
                AmoAPI::oAuth2(self::$config->widget()->getSubDomain(), self::$config->widget()->getId(), self::$config->widget()->getSecret(), self::$config->widget()->getUrl(), $code);
            } else {
                AmoAPI::oAuth2(self::$config->widget()->getSubDomain());
            }

        } catch (AmoAPIException $e) {
            self::$debugger->error(['Ошибка авторизации (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage()]);
        } catch (Exception $e) {
            self::$debugger->error(['Ошибка обработки токенов (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage()]);
        }

    }

    public function getContactByPhone (string $phone): array {

        $contacts = AmoAPI::getContacts([
            'query' => $phone
        ]);

        if (count($contacts) > 0) {
            return $contacts[0];
        }

        return [
            "id" => 0
        ];
    }

    public function createContact (string $name, string $phone, string $email = ""): int {
        $contact = new AmoContact([
            'name' => $name
        ]);

        $fields = [];

        $fields[self::$config->contact()->getPhoneFieldId()] = [[
            'value' => $phone,
            'enum'  => 'WORK'
        ]];

        if (strlen($email) > 0) {
            $fields[self::$config->contact()->getEmailFieldId()] = [[
                'value' => $email,
                'enum'  => 'WORK'
            ]];
        }

        $contact->setCustomFields($fields);

        try {
            return $contact->save();
        } catch (Exception $e) {
            self::$debugger->error(['Ошибка при создании контакта (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage()]);
        }

        return 0;
    }

    public function updateContactIfEmailExists(array $__contact, string $name, string $email): int
    {
        if ($__contact['name'] != $name || strlen($email) > 0) {
            $contact = new AmoContact([
                'id' => $__contact['id'],
                'name' => $name == self::$config->contact()->getDefaultName() ? $__contact['name'] : $name
            ]);

            if (strlen($email) > 0) {
                $contact->setCustomFields([
                    self::$config->contact()->getEmailFieldId() => [[
                        'value' => $email,
                        'enum'  => 'WORK'
                    ]]
                ]);
            }
            try {
                return $contact->save();
            } catch (Exception $e) {
                self::$debugger->error(['Ошибка при изменении контакта (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage()]);
            }
        }

        return $__contact['id'];
    }

    public function checkAndAuth () {
        if ($this->isInvalidStateForAuth()) {
            $this->locateToAmo();
        }

        $this->auth($_GET['code']);
    }

    public function createOrGetContact (string $name, string $phone, string $email = ""): int
    {
        $normalizedPhone = self::$phoneNormalizer->normalizePhone($phone);

        $gotContact = $this->getContactByPhone($normalizedPhone);

        if ($gotContact['id'] > 0) {
            return $this->updateContactIfEmailExists($gotContact, $name, $email);
        }

        return $this->createContact($name, $normalizedPhone, $email);
    }

    public function createLeadWithContact (AmoLead $lead, string $contactName, string $contactPhone, string $contactEmail): AmoLead
    {
        $contact = $this->createOrGetContact($contactName, $contactPhone, $contactEmail);

        $lead->addContacts($contact);

        return $lead;
    }

}