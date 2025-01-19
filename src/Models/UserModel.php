<?php
namespace App\Models;
use App\temp_dir\DatabaseModel;

class UserModel {

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    /**
     * Создает пользователя.
     *
     * @param string $login Логин пользователя.
     * @param string $password Пароль пользователя.
     * @return int|bool Возвращает id пользователя при успешном добавлении или false.
     */
    public function create($login, $password) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        if(!$this->checkUserExists($login)) {
            return $this->db->create('users', ['login' => $login, 'password' => $password]);
        } else {
            return false;
        }
    }
    /**
     * Удаляет пользователя
     *
     * @param string $login Логин пользователя.
     * @return bool Возвращает true при успешном удалении пользователя или false.
     */
    public function delete($login) {
        return $this->db->delete('users', ['login' => $login]);
    }

    /**
     * Проверяет существует ли пользователь.
     *
     * @param string $login Логин пользователя.
     * @return bool Возвращает true если пользователь существует или false при его отсутствии.
     */
    private function checkUserExists($login)
    {
        if ($this->db->read('users', ['login'], ['login' => $login])) {
            return true;
        } else {
            return false;
        }
    }
}