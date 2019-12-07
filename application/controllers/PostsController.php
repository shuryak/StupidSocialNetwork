<?php

namespace application\controllers;
use application\core\Controller;
use application\core\ErrorCodes;
use application\lib\Token;
use application\lib\JsonData;

class PostsController extends Controller {

    const MIN_POST_LENGTH = 1;
    const MAX_POST_LENGTH = 128;

    public static function postApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['access_token', 'content', 'attachments']);

        if(empty($dataCheckResult) || (count($dataCheckResult) == 1 && !isset($dataCheckResult['attachments']))) {
            $accessToken = $data->access_token;
            $content = strip_tags(trim($data->content));
            $attachments = !isset($dataCheckResult['attachments']) ? '' : $data->attachments;

            $badFields = [];

            if(iconv_strlen($content) < self::MIN_POST_LENGTH || iconv_strlen($content) > self::MAX_POST_LENGTH) {
                $badFields['content'] = ['min' => self::MIN_POST_LENGTH, 'max' => self::MAX_POST_LENGTH];
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

            $decodedToken = Token::decodeAccessToken($accessToken);

            if(isset($decodedToken['content'])) {
                $result = self::$model::makePost($decodedToken['content']['id'], $content, $attachments);

                if(isset($result['response'])) {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'response' => true,
                        )
                    );
                } else {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                        )
                    );
                }
            } elseif(isset($decodedToken['error'])) {
                if($decodedToken['error'] == Token::EXPIRED_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::EXPIRED_ACCESS_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
                if($decodedToken['error'] == Token::INVALID_TOKEN) {
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

    public static function getLastUserPostsApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['id', 'offset', 'start']);

        if(empty($dataCheckResult) || (count($dataCheckResult) == 1 && !isset($dataCheckResult['start']))) {
            $id = (int)$data->id;
            $offset = (int)$data->offset;
            $start = isset($dataCheckResult['start']) ? 0 : (int)$data->start;

            $result = self::$model::getLastUserPosts($id, $offset, $start);
            $postsCount = self::$model::getPostsCount($id);

            if(isset($result['response']) && isset($postsCount['response'])) {
                $leftCount = $start >= $postsCount['response'] ? 0 : $postsCount['response'] - ($start + count($result['response']));
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => [
                            'left' => $leftCount,
                            'posts' => $result['response'],
                        ]
                    )
                );
            } elseif(isset($result['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                    )
                );
            } elseif(isset($postsCount['error_code'])) {
                http_response_code($postsCount['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $postsCount['error_code'], 'details' => $postsCount['details']],
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

    public static function getPostByIdApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['post_id']);

        if(empty($dataCheckResult)) {
            $postId = (int)$data->post_id;

            $result = self::$model::getUserPost($postId);

            if(isset($result['response'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => $result['response'],
                    )
                );
            } else {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
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

    public static function deletePostApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['access_token', 'post_id']);

        if(empty($dataCheckResult)) {
            $accessToken = $data->access_token;
            $postId = (int)$data->post_id;

            $decodedToken = Token::decodeAccessToken($accessToken);

            if(isset($decodedToken['content'])) {
                $postData = self::$model::getUserPost($postId);
                if(isset($postData['response'])) {
                    if($postData['response']['author'] == 0) {
                        http_response_code(400);
                        echo json_encode(
                            array(
                                'error' => ['error_code' => ErrorCodes::POST_DOES_NOT_EXIST, 'details' => $postId],
                            )
                        );
                        return;
                    }

                    if($postData['response']['author'] == $decodedToken['content']['id']) {
                        $result = self::$model::deletePostById($postId);

                        if(isset($result['response'])) {
                            http_response_code($result['http']);
                            echo json_encode(
                                array(
                                    'response' => true,
                                )
                            );
                        } else {
                            http_response_code($result['http']);
                            echo json_encode(
                                array(
                                    'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                                )
                            );
                        }
                    } else {
                        http_response_code(403);
                        echo json_encode(
                            array(
                                'error' => ['error_code' => ErrorCodes::ANOTHER_POST, 'details' => $postId],
                            )
                        );
                    }
                } else {
                    http_response_code($postData['http']);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => $postData['error_code'], 'details' => $postData['details']],
                        )
                    );
                }
            } elseif(isset($decodedToken['error'])) {
                if($decodedToken['error'] == Token::EXPIRED_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::EXPIRED_REFRESH_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
                if($decodedToken['error'] == Token::INVALID_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::INVALID_REFRESH_TOKEN, 'details' => $accessToken],
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

    public static function getUserPostsCountApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['id']);

        if(empty($dataCheckResult)) {
            $id = (int)$data->id;

            $result = self::$model::getPostsCount($id);

            if(isset($result['response'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => $result['response'],
                    )
                );
            } else {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
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

}