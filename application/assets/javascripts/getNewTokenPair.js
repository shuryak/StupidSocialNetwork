async function getNewTokenPair() {
  const getNewTokenPairRequestUrl = '../api/users.getNewTokenPair';
  const getNewTokenPairRequestBody = {
    'refresh_token': localStorage.getItem('refresh_token')
  }

  await sendRequest('POST', getNewTokenPairRequestUrl, getNewTokenPairRequestBody)
    .then(data => {
      if(data.data.response) {
        console.log(data);
        localStorage.setItem('id', data.data.response.id);
        localStorage.setItem('access_token', data.data.response.new_access_token);
        localStorage.setItem('refresh_token', data.data.response.new_refresh_token);
        localStorage.setItem('expires_in', data.data.response.expires_in);
      } else {
        localStorage.clear();
        location.reload();
      }
    })
    .catch(err => {
      localStorage.clear();
      location.reload();
    })
}