(function () {
  'use strict';

  function openCreateModal() {
    // build modal inner content node (will be appended inside modal by modal(context))
    var content = document.createElement('div');
    content.className = 'create-modal-content';

    var header = document.createElement('h2');
    header.innerText = 'Upload photos from computer';
    header.style.margin = '0 0 8px 0';
    header.style.fontSize = '18px';
    header.style.color = '#fff';

    var description = document.createElement('p');
    description.innerText = '';
    description.style.margin = '0 0 16px 0';
    description.style.color = '#bbb';

    // upload handler for createUploadButton: receives event where e.target is the input
    var handleUpload = function (e) {
      var input = e.target;
      var files = input.files;

      if (!files || files.length === 0) {
        notify('No files selected', 'error');
        return;
      }

      var formData = new FormData();
      // append each file as image[] so the backend can accept multiple files
      for (var i = 0; i < files.length; i++) {
        formData.append('image[]', files[i]);
      }

      // find the upload container (the parent returned by createUploadButton)
      var uploadContainer = input.closest && input.closest('div') ? input.closest('div') : null;

      try {
        if (uploadContainer) spinner(uploadContainer);
      } catch (err) {
        // ignore spinner errors
      }

      httpClient
        .upload('upload/gallery', formData)
        .then(function (response) {
          try { if (uploadContainer) spinner(uploadContainer); } catch (e) {}
          notify((response && response.message) ? response.message : 'Upload successful', 'success');
          // close modal by triggering the close button inside modal
          var closeBtn = document.querySelector('.modal .modal-close');
          if (closeBtn) closeBtn.click();
          // Optionally: emit a custom event so the profile/gallery can refresh
          var event = new CustomEvent('gallery:uploaded', { detail: response });
          document.dispatchEvent(event);
          // Reload the page so new photos are visible on main/profile
          try {
            window.location.reload();
          } catch (e) {
            // ignore reload errors
          }
        })
        .catch(function (err) {
          try { if (uploadContainer) spinner(uploadContainer); } catch (e) {}
          var msg = (err && err.message) ? err.message : (err && err.error) ? err.error : 'Upload failed';
          notify(msg, 'error');
        });
    };

    // create upload UI using existing component helper
    var uploadUI = null;
    try {
      uploadUI = createUploadButton(handleUpload);
    } catch (err) {
      // fallback: create basic file input
      uploadUI = document.createElement('div');
      var fallbackInput = document.createElement('input');
      fallbackInput.type = 'file';
      fallbackInput.accept = 'image/*';
      fallbackInput.multiple = true;
      fallbackInput.onchange = handleUpload;
      uploadUI.appendChild(fallbackInput);
    }

    // small note about allowed types
    var hint = document.createElement('small');
    hint.style.display = 'block';
    hint.style.marginTop = '10px';
    hint.style.color = '#999';
    hint.innerText = 'Supported formats: JPG, PNG. Max file size depends on server configuration.';

    content.appendChild(header);
    content.appendChild(description);
    content.appendChild(uploadUI);
    content.appendChild(hint);

    // show using shared modal helper
    modal(content);
  }

  function init() {
    var triggers = document.querySelectorAll('.create-trigger');

    if (!triggers || !triggers.length) {
      // fallback: try to find a nav link whose label text is 'Create'
      var links = Array.from(document.querySelectorAll('.app-sidebar .nav-link'));
      for (var i = 0; i < links.length; i++) {
        var lbl = links[i].querySelector('.nav-label');
        if (lbl && lbl.textContent.trim() === 'Create') {
          triggers = [links[i]];
          break;
        }
      }
    }

    Array.prototype.forEach.call(triggers, function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        openCreateModal();
      });
    });

    // Optionally listen for gallery:uploaded to refresh UI (other modules may subscribe)
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();