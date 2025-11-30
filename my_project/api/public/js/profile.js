(function () {
  'use strict';

  function createImageCard(item) {
    var wrapper = document.createElement('div');
    wrapper.className = 'profile-card';
    var img = document.createElement('img');
    img.src = item.file;
    img.alt = item.username ? item.username + ' image' : 'image';
    img.loading = 'lazy';
    img.className = 'profile-image';
    wrapper.appendChild(img);
    return wrapper;
  }

  async function fetchImages(userId, type) {
    // type: 'posts' -> author=userId, 'liked' -> user_id=userId
    try {
      var params = 'items_per_page=0';
      if (type === 'liked') {
        params += '&user_id=' + encodeURIComponent(userId);
      } else {
        params += '&author=' + encodeURIComponent(userId);
      }
      var url = '/gallery?' + params;
      var res = await fetch(url, { credentials: 'same-origin' });
      if (!res.ok) throw new Error('Failed to load images');
      var data = await res.json();
      return data;
    } catch (err) {
      console.error(err);
      if (typeof notify === 'function') notify('Не удалось загрузить изображения', 'error');
      return [];
    }
  }

  async function renderGallery(type) {
    var galleryEl = document.getElementById('profile-gallery');
    if (!galleryEl) return;
    galleryEl.innerHTML = '';

    var userId = window.__PROFILE_USER_ID__ || null;
    if (!userId) return;

    var images = await fetchImages(userId, type);

    galleryEl.innerHTML = '';

    var emptyContainer = document.getElementById('profile-empty');
    if (emptyContainer) emptyContainer.innerHTML = '';

    if (!images || !images.length) {
      var empty = document.createElement('div');
      empty.className = 'no-photos w-100 text-center';

      // Inline SVG (simple camera/placeholder icon)
      var svg = '<svg width="72" height="72" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">'
        + '<path d="M21 7h-3.172l-1.414-1.414A2 2 0 0 0 15.172 5H8.828a2 2 0 0 0-1.242.586L6.172 7H3a1 1 0 0 0-1 1v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1zM12 18a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" fill="#8b8f96"/>'
        + '<circle cx="12" cy="10" r="2" fill="#cfd2d6" opacity="0.6"/>'
        + '</svg>';

      var title = document.createElement('p');
      title.className = 'mt-3 mb-1';
      title.innerText = 'There are no photos here yet';

      var subtitle = document.createElement('small');
      subtitle.className = 'text-muted mb-0';
      subtitle.innerText = 'Upload your first photo from your computer';

      // Inject SVG and text
      empty.innerHTML = svg;
      empty.appendChild(title);
      empty.appendChild(subtitle);

      if (emptyContainer) {
        emptyContainer.appendChild(empty);
      } else {
        galleryEl.appendChild(empty);
      }

      return;
    }

    images.forEach(function (item) {
      var card = createImageCard(item);
      var imgEl = card.querySelector('img');
      if (imgEl) {
        imgEl.dataset.gallery = item.id;
          if (type === 'posts') {
            // pass option allowDelete so modal can show delete button on profile
            imgEl.addEventListener('click', function (e) { handleModal(e, { allowDelete: true }); });
          }
      }
      galleryEl.appendChild(card);
    });
  }

  function initTabs() {
    var tabs = document.querySelectorAll('.profile-tabs .tab');
    Array.prototype.forEach.call(tabs, function (tab) {
      tab.addEventListener('click', function (e) {
        e.preventDefault();
        var t = tab.dataset.tab;
        // toggle active class
        tabs.forEach(function (s) { s.classList.remove('active'); });
        tab.classList.add('active');
        renderGallery(t);
      });
    });
  }

  async function init() {
    initTabs();
    // load posts by default
    await renderGallery('posts');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
