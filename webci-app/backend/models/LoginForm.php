<?php

namespace backend\models;

use common\models\AdminUser;
use Yii;
use yii\base\Model;

/**
 * Login form for backend administrators.
 */
class LoginForm extends Model
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = true;

    private ?AdminUser $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Usuario o contraseña incorrectos.');
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
            $success = Yii::$app->user->login($this->getUser(), $duration);
            if ($success) {
                $this->getUser()->updateAttributes(['last_login_at' => time()]);
            }
            return $success;
        }

        return false;
    }

    private function getUser(): ?AdminUser
    {
        if ($this->_user === null) {
            $this->_user = AdminUser::findByUsername($this->username);
        }
        return $this->_user;
    }
}

