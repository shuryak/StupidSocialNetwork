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

  let currentUser = {};

  const mainRequestUrl = '../api/users.getUser';
  const mainRequestBody = {
    id: +urlParams.id
  }

  sendRequest('POST', mainRequestUrl, mainRequestBody)
    .then(data => {
      if(data.data.response) {
        currentUser = new User(+data.data.response.id, data.data.response.firstname, data.data.response.lastname, data.data.response.email);

        const fullNamePlace = document.querySelector('.right__name');
        fullNamePlace.textContent = currentUser.getFullName();
        const emailPlace = document.querySelector('.right__email');
        emailPlace.textContent = currentUser.email;

        showLastUserPosts(+urlParams.id, 10, 0);

        const postButton = document.querySelector('#post-button');
        postButton.onclick = makePost;
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
}

async function makePost() {
  if(localStorage.getItem('expires_in') * 1000 <= Date.now()) {
    await getNewTokenPair();
  }
  
  const makePostRequestUrl = '../api/posts.post';
  const makePostRequestBody = {
    'access_token': localStorage.getItem('access_token'),
    'content': document.querySelector('#post-field').value
  }

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
  }

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