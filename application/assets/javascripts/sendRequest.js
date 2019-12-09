function sendRequest(method, url, body = null) {
  const headers = {
    'Content-Type': 'application/json',
  };

  return fetch(url, {
    method: method,
    body: JSON.stringify(body),
    headers: headers
  }).then(async (response) => ({
    status: response.status,
    data: await response.json(),
  }));
};