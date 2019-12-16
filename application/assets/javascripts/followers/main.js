let urlParams = getUrlParams();

if(!localStorage.getItem('id') || !localStorage.getItem('access_token')) {
  localStorage.clear();
  location.href = '../users/login';
} else if(!urlParams.id) {
  location.search = '?id=' + localStorage.getItem('id');
} else if(!Number.isInteger(+urlParams.id) || urlParams.id <= 0) {
  const mb = [
    { text: 'OK', click() { this.hide() } },
  ];

  const errModal = new Modal('Неверные параметры в адресной строке.', mb);
  errModal.show();
} else {
  buildNavMenu(+localStorage.getItem('id'));

  showUserFollowers(+urlParams.id, 10, 0);
}

function showUserFollowers(id, offset, start) {
  const followerPlace = document.querySelector('.all__followers');
  const getUserFollowersRequestUrl = '../api/followers.getUserFollowers';
  const getUserFollowersRequestBody = {
    'id': id,
    'offset': offset,
    'start': start
  };

  sendRequest('POST', getUserFollowersRequestUrl, getUserFollowersRequestBody)
    .then(data => {
      console.log(data);
      if(data.data.response) {
        data.data.response.followers.forEach(follower => {
          const followerItem = createElement('div', {
            class: 'user',
            'data-id': follower.id,
            'data-is-friends': 0
          }, [
            createElement('img', {
              class: 'user__avatar'
            }),
            createElement('div', {
              class: 'user__info'
            }, [
              createElement('p', {
                class: 'user__name',
                textContent: follower.firstname + ' ' + follower.lastname
              }),
              createElement('button', {
                class: 'user__follow ssn-button',
                textContent: 'Добавить в друзья',
                onclick: () => {
                  follow(follower.id)
                }
              })
            ])
          ])

          const followerAvatar = followerItem.querySelector('.user__avatar');
          followerAvatar.setAttribute('src', '../application/assets/images/avatar.png');

          followerPlace.append(followerItem);
        });
      }
    })
    .catch(err => {
      const mb = [
        { text: 'OK', click() { this.hide() } },
      ];
  
      const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
      errModal.show();
    })
}

async function follow(following) {
  if(localStorage.getItem('expires_in') * 1000 <= Date.now()) {
    await getNewTokenPair();
  }

  const followRequestUrl = '../api/followers.follow';
  const followRequestBody = {
    access_token: localStorage.getItem('access_token'),
    following: following
  };

  sendRequest('POST', followRequestUrl, followRequestBody)
  .then(data => {
    console.log(data);
    if(data.data.response) {
      changeFollowStatus(following);
    } else if(data.data.error) {
      const mb = [
        { text: 'OK', click() { this.hide() } },
      ];
  
      const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
      errModal.show();
    }
  })
  .catch(err => {
    const mb = [
      { text: 'OK', click() { this.hide() } },
    ];

    const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
    errModal.show();
  })
}

async function unfollow(following) {
  if(localStorage.getItem('expires_in') * 1000 <= Date.now()) {
    await getNewTokenPair();
  }

  const followRequestUrl = '../api/followers.unfollow';
  const followRequestBody = {
    access_token: localStorage.getItem('access_token'),
    following: following
  };

  sendRequest('POST', followRequestUrl, followRequestBody)
  .then(data => {
    console.log(data);
    if(data.data.response) {
      changeFollowStatus(following);
    } else if(data.data.error) {
      const mb = [
        { text: 'OK', click() { this.hide() } },
      ];
  
      const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
      errModal.show();
    }
  })
  .catch(err => {
    const mb = [
      { text: 'OK', click() { this.hide() } },
    ];

    const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
    errModal.show();
  })
}

function changeFollowStatus(following) {
  const curUser = document.querySelector(`.user[data-id="${following}"]`);
  curButton = curUser.querySelector('.user__follow');
  if(curUser.getAttribute('data-is-friends') == 0) {
    console.log('1');
    curUser.setAttribute('data-is-friends', 1);
    curButton.classList.add('ssn-button-pressed');
    curButton.textContent = 'Удалить из друзей';
    curButton.onclick = () => {
      unfollow(following);
    }
  } else if(curUser.getAttribute('data-is-friends') == 1) {
    console.log('0');
    curUser.setAttribute('data-is-friends', 0);
    curButton.classList.remove('ssn-button-pressed');
    curButton.textContent = 'Добавить в друзья';
    curButton.onclick = () => {
      follow(following);
    }
  }
}