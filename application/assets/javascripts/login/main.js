if(localStorage.getItem('id') || localStorage.getItem('access_token')) {
  location.href = '../users/profile';
}

const loginButton = document.querySelector('#login-button');

loginButton.addEventListener('click', () => {
  const emailField = document.querySelector('#email-field').value;
  const passwordField = document.querySelector('#password-field').value;

  const loginRequestUrl = '../api/users.login';
  const loginRequestBody = {
    email: emailField,
    password: passwordField
  }

  sendRequest('POST', loginRequestUrl, loginRequestBody)
    .then(data => {
      if(data.data.response) {
        localStorage.setItem('id', data.data.response.id);
        localStorage.setItem('access_token', data.data.response.access_token);
        localStorage.setItem('refresh_token', data.data.response.refresh_token);
        localStorage.setItem('expires_in', data.data.response.expires_in);

        location.href = '../users/profile';
      } else {
        const mb = [
          { text: 'OK', click() { this.hide() } },
        ];
  
        const errModal = new Modal('Пользователь с таким Email или паролем не зарегистрирован.', mb);
        errModal.show();
      }
    })
    .catch(err => {
      const mb = [
        { text: 'OK', click() { this.hide() } },
      ];

      const errModal = new Modal('Ошибка доступа.', mb);
      errModal.show();
    });
});