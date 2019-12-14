let urlParams = getUrlParams();
let currentUser = {};

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

  const mainRequestUrl = '../api/users.getUser';
  const mainRequestBody = {
    id: +urlParams.id
  };

  sendRequest('POST', mainRequestUrl, mainRequestBody)
    .then(data => {
      if(data.data.response) {
        currentUser = new User(+data.data.response.id, data.data.response.firstname, data.data.response.lastname, data.data.response.email);

        const fullNamePlace = document.querySelector('.right__name');
        fullNamePlace.textContent = currentUser.getFullName();
        const emailPlace = document.querySelector('.right__email');
        emailPlace.textContent = currentUser.email;

        showLastUserPosts(+urlParams.id, 10, 0);
      } else if (data.data.error.error_code == errorCodes.ID_IS_NOT_REGISTERED && +urlParams.id == localStorage.getItem('id')) {
        const mb = [
          { text: 'OK', click() { this.hide() } },
        ];

        const errModal = new Modal('Ошибка идентификации.', mb);
        errModal.show();
      } else {
        location.search = '?id=' + localStorage.getItem('id');
      }
    })
    .catch(err => {
      const mb = [
        { text: 'OK', click() { this.hide() } },
      ];

      const errModal = new Modal('Ошибка идентификации.', mb);
      errModal.show();
    });

    if(+localStorage.getItem('id') == +urlParams.id) {
      showPostingFields();
    } else {
      followButtonBuilder();
    }
}

async function makePost() {
  if(localStorage.getItem('expires_in') * 1000 <= Date.now()) {
    await getNewTokenPair();
  }
  
  const makePostRequestUrl = '../api/posts.post';
  const makePostRequestBody = {
    'access_token': localStorage.getItem('access_token'),
    'content': document.querySelector('#post-field').value
  };

  sendRequest('POST', makePostRequestUrl, makePostRequestBody)
    .then(data =>{
      console.log(data);
      if(data.data.response) {
        showLastUserPosts(localStorage.getItem('id'), 10, 0);
      } else {
        const mb = [
          { text: 'OK', click() { this.hide() } },
        ];
    
        const errModal = new Modal('Ошибка отправки формы.', mb);
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

function showLastUserPosts(id, offset, start) {
  const postPlace = document.querySelector('.right__posts');
  postPlace.innerHTML = '';

  const getPostsRequestUrl = '../api/posts.getLastUserPosts';
  const getPostsRequestBody = {
    'id': id,
    'offset': offset,
    'start': start
  };

  sendRequest('POST', getPostsRequestUrl, getPostsRequestBody)
    .then(data => {
      console.log(data);
      if(data.data.response) {
        data.data.response.posts.forEach(post => {
          const postItem = createElement('div', {
          class: 'post',
          'data-id': post.post_id
          }, [
            createElement('p', {
              class: 'post__date'
            }),
            createElement('p', {
              class: 'post__text'
            })
          ]);

          const postDate = postItem.querySelector('.post__date');
          const postText = postItem.querySelector('.post__text');

          const normalDate = new Date(post.time * 1000);

          postDate.textContent = normalDate.toLocaleString('ru', { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' });
          postText.textContent = post.content;

          postPlace.append(postItem);
        });
      }
    })
    .catch(err => {
      const mb = [
        { text: 'OK', click() { this.hide() } },
      ];
  
      const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
      errModal.show();
    });
}

function showPostingFields() {
  const postingFields = createElement('div', {
    class: 'right__posting'
  }, [
    createElement('textarea', {
      name: 'post',
      id: 'post-field',
      class: 'right__posting-content ssn-textarea',
      maxlength: '128',
      placeholder: 'Что у Вас нового?'
    }),
    createElement('button', {
      class: 'right__posting-button ssn-button',
      id: 'post-button'
    })
  ]);

  postingFields.querySelector('.right__posting-button').textContent = 'Опубликовать';
  postingFields.querySelector('.right__posting-button').onclick = makePost;

  const postingFieldsPlace = document.querySelector('.right__top');
  postingFieldsPlace.after(postingFields);
}

function followButtonBuilder() {
  if(document.querySelector('.left__follow')) {
    document.querySelector('.left__follow').remove();
  }

  if(document.querySelector('.left__follow-status')) {
    document.querySelector('.left__follow-status').remove();
  }

  const isFollowedRequestUrl = '../api/followers.isFollowed';
  const isFollowedRequestBody = {
    follower: +localStorage.getItem('id'),
    following: +urlParams.id
  };

  const followButton = createElement('button', {
    class: 'left__follow ssn-button'
  });

  const followStatus = createElement('p', {
    class: 'left__follow-status'
  });

  let followText = '';

  sendRequest('POST', isFollowedRequestUrl, isFollowedRequestBody)
    .then(data => {
      console.log(data);
      if(data.data.response == 0) {
        followText = 'Подписаться';
        followButton.onclick = follow;
      } else if(data.data.response == 1) {
        followText = 'Отписаться';
        followButton.onclick = unfollow;
        followButton.classList.add('ssn-button-pressed');
      } else if(data.data.response == 2) {
        followText = 'Удалить из друзей';
        followButton.onclick = unfollow;
        followButton.classList.add('ssn-button-pressed');
      } else if(data.data.response == 3) {
        followText = 'Добавить в друзья';
        followButton.onclick = follow;
        followStatus.textContent = currentUser.firstname + ' подписан на Вас.';
      } else if(data.data.error) {
        const mb = [
          { text: 'OK', click() { this.hide() } },
        ];
    
        const errModal = new Modal('Произошла ошибка. Перезагрузите страницу.', mb);
        errModal.show();
      }

      followButton.textContent = followText;

      const followButtonPlace = document.querySelector('.left');
      followButtonPlace.append(followButton);

      if(followStatus.textContent) {
        followButtonPlace.append(followStatus);
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

async function unfollow() {
  if(localStorage.getItem('expires_in') * 1000 <= Date.now()) {
    await getNewTokenPair();
  }

  const unfollowRequestUrl = '../api/followers.unfollow';
  const unfollowRequestBody = {
    access_token: localStorage.getItem('access_token'),
    following: +urlParams.id
  };

  sendRequest('POST', unfollowRequestUrl, unfollowRequestBody)
    .then(data => {
      if(data.data.response) {
        followButtonBuilder();
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

async function follow() {
  if(localStorage.getItem('expires_in') * 1000 <= Date.now()) {
    await getNewTokenPair();
  }

  const followRequestUrl = '../api/followers.follow';
  const followRequestBody = {
    access_token: localStorage.getItem('access_token'),
    following: +urlParams.id
  };

  sendRequest('POST', followRequestUrl, followRequestBody)
    .then(data => {
      console.log(data);
      if(data.data.response) {
        followButtonBuilder();
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