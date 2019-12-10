function buildNavMenu(id) {
  const navContent = document.querySelector('.nav__list');

  const navProfile = navContent.querySelector('#nav-profile');
  navProfile.onclick = () => {
    location.search = '?id=' + id;
  }

  const navLogout = navContent.querySelector('#nav-logout');
  navLogout.onclick = () => {
    localStorage.clear();
    location.reload();
  }
}