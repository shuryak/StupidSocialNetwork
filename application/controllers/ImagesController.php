<?php

namespace application\controllers;
use application\core\Controller;
use application\core\ErrorCodes;
use application\lib\Token;
use application\lib\JsonData;
use Exception;

class ImagesController extends Controller {

    public static function uploadApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        if(isset($_FILES['image']) && isset($_POST['access_token'])) {
            if($_FILES['image']['size'] > 1048576 || $_FILES['image']['size'] <= 0) {
                http_response_code(413);
                echo json_encode(
                    array(
                        'error' => ['error_code' => ErrorCodes::FILE_IS_TOO_LARGE, 'details' => $_FILES['image']['size']],
                    )
                );
                return;
            }

            $accessToken = $_POST['access_token'];

            $decoded = Token::decodeAccessToken($accessToken);

            if(isset($decoded['content'])) {
                try {
                    $result = self::$model::upload($decoded['content']['id'], $_FILES['image']['tmp_name']);
                } catch(Exception $e) {
                    http_response_code(500);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => 1, 'details' => false],
                        )
                    );
                    return;
                }
                if(isset($result['response'])) {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'response' => [
                                'path' => $result['response'],
                            ],
                        )
                    );
                } else {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => $userInformation['error_code'], 'details' => false],
                        )
                    );
                }
            } elseif(isset($decoded['error'])) {
                if($decoded['error'] == Token::EXPIRED_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::EXPIRED_ACCESS_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
                if($decoded['error'] == Token::INVALID_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::INVALID_ACCESS_TOKEN, 'details' => $accessToken],
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