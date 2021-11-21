<?php

namespace Befew;

use Exception;

class Entity {
    private array $uniqueKeyInfo = array();

    /**
     * @param string $method
     * @param array $args
     *
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $args) {
        if (substr($method, 0, 3) == 'get') {
            return $this->get(lcfirst(str_replace('get', '', $method)));
        } else if (substr($method, 0, 3) == 'set') {
            return $this->set(lcfirst(str_replace('set', '', $method)), $args[0]);
        } else {
            throw new Exception('Unknown method "' . $method . '" called');
        }
    }

    public function get(string $element) {
        return $this->{$this->snakeCaseToCamelCase($element)};
    }

    public function set(string $element, $value): Entity {
        $this->{$this->snakeCaseToCamelCase($element)} = $value;

        return $this;
    }

    /**
     * Generates a unique key
     * Less than one chance over 1.343646e+111 to bump into an existing key. If it happens, it just generates another key.
     *
     * @return string
     * @throws Exception
     */
    public function generateUniqId(): string {
        if (count($this->uniqueKeyInfo) != 3) {
            throw new Exception('Error while generating unique key: please fill the informations first by calling Entity::setUniqueIdInfo');
        } else {
            $query = null;
            $key = '';
            $chars = array(
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
            );

            do {
                while (strlen($key) < $this->uniqueKeyInfo['length']) {
                    $key .= $chars[rand(0, count($chars))];
                }

                $query = Db::getInstance()->getDBH()->prepare(
                    'SELECT * FROM ' . $this->uniqueKeyInfo['table'] . ' WHERE ' . $this->uniqueKeyInfo['key'] . ' = :keyvalue'
                );

                $query->execute(array(
                    'keyvalue' => $key
                ));
            } while ($query->rowCount() > 0);

            return $key;
        }
    }

    protected function setUniqueIdInfo(string $tableName, string $keyName, int $length): void {
        $this->uniqueKeyInfo['table'] = $tableName;
        $this->uniqueKeyInfo['key'] = $keyName;
        $this->uniqueKeyInfo['length'] = $length;
    }

    private function __construct(array $data) {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    private function snakeCaseToCamelCase(string $string): string {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }
}