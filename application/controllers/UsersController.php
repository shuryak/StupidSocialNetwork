<?php

namespace application\controllers;
use application\core\Controller;
use application\core\ErrorCodes;
use application\lib\Token;
use application\lib\JsonData;
use Exception;

class UsersController extends Controller {

    const MIN_FIRSTNAME_LENGTH = 1;
    const MAX_FIRSTNAME_LENGTH = 32;

    const MIN_LASTNAME_LENGTH = 1;
    const MAX_LASTNAME_LENGTH = 32;

    const MIN_PASSWORD_LENGTH = 6;

    public static function profileAction() {
        $scripts = [
            'createElement.js',
            'buildNavMenu.js',
            'sendRequest.js',
            'getUrlParams.js',
            'profile/User.js',
            'profile/main.js'
        ];

        self::$view::show('SSN. Профиль.', 'standard', $scripts);
    }

    public static function registerApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));
        
        $dataCheckResult = JsonData::check($data, ['firstname', 'lastname', 'email', 'password']);

        if(empty($dataCheckResult)) {
            $firstname = strip_tags(trim($data->firstname));
            $lastname = strip_tags(trim($data->lastname));
            $email = strip_tags(trim($data->email));
            $password = htmlspecialchars($data->password);

            $badFields = [];

            if(strlen($firstname) < self::MIN_FIRSTNAME_LENGTH || strlen($data->firstname) > self::MAX_FIRSTNAME_LENGTH) {
                $badFields['firstname'] = ['min' => self::MIN_FIRSTNAME_LENGTH, 'max' => self::MAX_FIRSTNAME_LENGTH];
            }

            if(strlen($lastname) < self::MIN_LASTNAME_LENGTH || strlen($data->firstname) > self::MAX_LASTNAME_LENGTH) {
                $badFields['lastname'] = ['min' => self::MIN_LASTNAME_LENGTH, 'max' => self::MAX_LASTNAME_LENGTH];
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $badFields['email'] = false;
            }

            if(strlen($password) < self::MIN_PASSWORD_LENGTH) {
                $badFields['password'] = ['min' => self::MIN_PASSWORD_LENGTH];
            }

            if(!empty($badFields)) {
                http_response_code(400);
                echo json_encode(
                    array(
                        'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => $badFields],
                    )
                );

                return;
            }

            try {
                $result = self::$model::createUser($firstname, $lastname, $email, $password);
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(
                    array(
                        'error' => ['error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR, 'details' => false],
                    )
                );

                return;
            }

            if(isset($result['response'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => $result['response'],
                    )
                );
                
                return;
            }

            http_response_code($result['http']);
            echo json_encode(
                array(
                    'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                )
            );
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function loginApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['email', 'password']);

        if(empty($dataCheckResult)) {
            $email = strip_tags(trim($data->email));
            $password = htmlspecialchars($data->password);

            $userInformation = self::$model::getUserInformationByEmail($email);

            if(isset($userInformation['response']) && password_verify($password, $userInformation['response']['password'])) {
                $accessTokenContent = array(
                    'id' => $userInformation['response']['id'],
                    'firstname' => $userInformation['response']['firstname'],
                    'lastname' => $userInformation['response']['lastname'],
                    'email' => $userInformation['response']['email'],
                );

                $refreshTokenContent = array(
                    'id' => $userInformation['response']['id'],
                );

                $access = Token::createAccessToken($accessTokenContent);

                if(!$userInformation['response']['refresh_token']) {
                    $refresh = Token::createRefreshToken($refreshTokenContent);
                    self::$model::addRefreshTokenToId($refresh, $userInformation['response']['id']);
                } else {
                    $refresh = $userInformation['response']['refresh_token'];
                }

                http_response_code($userInformation['http']);
                echo json_encode(
                    array(
                        'response' => [
                            'id' => $userInformation['response']['id'],
                            'access_token' => $access,
                            'refresh_token' => $refresh,
                        ],
                    )
                );
            } else {
                http_response_code($userInformation['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => ErrorCodes::INCORRECT_LOGIN_OR_PASSWORD, 'details' => false],
                    )
                );
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function getUserApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['id']);

        if(empty($dataCheckResult)) {
            $id = (int)$data->id;

            $userInformation = self::$model::getUserInformationById($id);

            if(isset($userInformation['response'])) {
                http_response_code($userInformation['http']);
                echo json_encode(
                    array(
                        'response' => [
                            'id' => $userInformation['response']['id'],
                            'status' => $userInformation['response']['status'],
                            'firstname' => $userInformation['response']['firstname'],
                            'lastname' => $userInformation['response']['lastname'],
                            'email' => $userInformation['response']['email'],
                        ],
                    )
                );
            } else {
                http_response_code($userInformation['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $userInformation['error_code'], 'details' => false],
                    )
                );
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function getNewTokenPairApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['refresh_token']);

        if(empty($dataCheckResult)) {
            $refreshToken = $data->refresh_token;

            $decoded = Token::decodeRefreshToken($refreshToken);

            if(isset($decoded['content'])) {
                $userInformation = self::$model::getUserInformationById($decoded['content']['id']);

                if(isset($userInformation['response'])) {
                    if($refreshToken == $userInformation['response']['refresh_token']) {
                        $accessTokenContent = array(
                            'id' => $userInformation['response']['id'],
                            'firstname' => $userInformation['response']['firstname'],
                            'lastname' => $userInformation['response']['lastname'],
                            'email' => $userInformation['response']['email'],
                        );

                        $refreshTokenContent = array(
                            'id' => $userInformation['response']['id'],
                        );

                        $newAccessToken = Token::createAccessToken($accessTokenContent);
                        $newRefreshToken = Token::createRefreshToken($refreshTokenContent);

                        $result = self::$model::addRefreshTokenToId($newRefreshToken, $decoded['content']['id']);

                        if(isset($result['response'])) {
                            http_response_code($result['http']);
                            echo json_encode(
                                array(
                                    'response' => [
                                        'new_access_token' => $newAccessToken,
                                        'new_refresh_token' => $newRefreshToken,
                                        'id' => $userInformation['response']['id'],
                                    ],
                                )
                            );
                            return;
                        } else {
                            http_response_code($result['error_code']);
                            echo json_encode(
                                array(
                                    'error' => ['error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR, 'details' => $result['details']],
                                )
                            );
                            return;
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(
                            array(
                                'error' => ['error_code' => ErrorCodes::USED_REFRESH_TOKEN, 'details' => $refreshToken],
                            )
                        );
                    }
                }
            } elseif(isset($decoded['error'])) {
                if($decoded['error'] == Token::EXPIRED_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::EXPIRED_REFRESH_TOKEN, 'details' => $refreshToken],
                        )
                    );
                    return;
                }
                if($decoded['error'] == Token::INVALID_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::INVALID_REFRESH_TOKEN, 'details' => $refreshToken],
                        )
                    );
                    return;
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }
}