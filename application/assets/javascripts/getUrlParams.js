function getUrlParams() {
  const params = {};
  window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
      params[key] = value;
  });
  return params;
}