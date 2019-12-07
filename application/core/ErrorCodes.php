<?php

namespace application\core;

abstract class ErrorCodes {
  const BAD_FIELDS = 0; // Пользователь заполнил не все необходимые поля при обращении
  const UNKNOWN_INTERNAL_ERROR = 1; // Странная серверная ошибка: например, нет коннекта к БД
  const EMAIL_ALREADY_EXISTS = 2; // Пользователь с таким Email уже заргистрирован
  const EMAIL_IS_NOT_REGISTERED = 3; // Пользователь с таким Email не зарегистрирован
  const ID_IS_NOT_REGISTERED = 4; // Пользователь с таким Id не зарегистрирован
  const INCORRECT_LOGIN_OR_PASSWORD = 5; // Неправильное имя пользователя или пароль
  const INVALID_ACCESS_TOKEN = 6; // Ошибочный access token
  const EXPIRED_ACCESS_TOKEN = 7; // Истекший access token
  const INVALID_REFRESH_TOKEN = 8; // Ошибочный refresh token
  const EXPIRED_REFRESH_TOKEN = 9; // Истекший refresh token
  const USED_REFRESH_TOKEN = 10; // Использованный refresh token
  const POST_DOES_NOT_EXIST = 11; // Пост с таким Id не существует
  const ANOTHER_POST = 12; // чужой пост (access_token не имеет доступа к этому посту)

  const USERS_ARE_ALREADY_FRIENDS = 13; // пользователи уже друзья
  const USER_IS_ALREADY_FOLLOWED = 14; // пользоваетль уже подписан
  const IDENTICAL_VALUES = 15; // идентичные значения, когда это недопустимо, например, подписка на самого себя
  const USER_IS_NOT_FOLLOWED = 16; // пользователь не подписан
  const FOLLOWERS_DOES_NOT_EXISTS = 17; // нет подписчиков
  const FRIENDS_DOES_NOT_EXISTS = 18; // нет друзей
  const FOLLOWING_DOES_NOT_EXISTS = 19; // нет подписок
}