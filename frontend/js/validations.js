// frontend/js/validations.js

function hasJQueryValidate() {
  const $ = window.jQuery || window.$;
  return !!($ && typeof $.fn?.validate === 'function');
}

function hasBlockUI() {
  const $ = window.jQuery || window.$;
  return !!($ && typeof $.blockUI === 'function' && typeof $.unblockUI === 'function');
}

function hasToastr() {
  const t = window.toastr;
  return !!(t && typeof t.success === 'function' && typeof t.error === 'function');
}

export function notifySuccess(message) {
  if (hasToastr()) return window.toastr.success(message);
  alert(message);
}

export function notifyError(message) {
  if (hasToastr()) return window.toastr.error(message);
  alert(message);
}

export function notifyInfo(message) {
  if (hasToastr()) return window.toastr.info(message);
}

export function confirmWithModal({
  title = 'Confirm',
  message = 'Are you sure?',
  okText = 'OK',
  okBtnClass = 'btn-danger'
} = {}) {
  const modalEl = document.getElementById('confirmActionModal');
  const titleEl = document.getElementById('confirmActionTitle');
  const msgEl = document.getElementById('confirmActionMessage');
  const okBtn = document.getElementById('confirmActionOkBtn');

  // Fallback if modal/bootstrap is not available
  if (!modalEl || !okBtn || !window.bootstrap?.Modal) {
    return Promise.resolve(confirm(`${title}\n\n${message}`));
  }

  if (titleEl) titleEl.textContent = title;
  if (msgEl) msgEl.textContent = message;

  okBtn.textContent = okText;
  okBtn.className = `btn ${okBtnClass}`;

  const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);

  return new Promise((resolve) => {
    const onOk = () => {
      cleanup();
      modal.hide();
      resolve(true);
    };
    const onHide = () => {
      cleanup();
      resolve(false);
    };
    const cleanup = () => {
      okBtn.removeEventListener('click', onOk);
      modalEl.removeEventListener('hidden.bs.modal', onHide);
    };

    okBtn.addEventListener('click', onOk, { once: true });
    modalEl.addEventListener('hidden.bs.modal', onHide, { once: true });
    modal.show();
  });
}

export function setupFormValidation(formSelector, rules, messages) {
  if (!hasJQueryValidate()) return;

  const $ = window.jQuery || window.$;
  const $form = $(formSelector);
  if (!$form.length) return;

  // prevent re-initializing
  if ($form.data('validator')) return;

  $form.validate({
    rules,
    messages,
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
      element.classList.add('is-invalid');
      element.classList.remove('is-valid');
    },
    unhighlight: function (element) {
      element.classList.remove('is-invalid');
      element.classList.add('is-valid');
    },
    errorPlacement: function (error, element) {
      // Bootstrap-friendly placement
      if (element.closest('.input-group').length) {
        error.insertAfter(element.closest('.input-group'));
      } else {
        error.insertAfter(element);
      }
    }
  });
}

export function isFormValid(formEl) {
  // Prefer jQuery Validate if present
  const $ = window.jQuery || window.$;
  if ($ && typeof $(formEl).valid === 'function' && $(formEl).data('validator')) {
    return $(formEl).valid();
  }

  // Fallback to HTML5 constraint validation
  if (typeof formEl.checkValidity === 'function') {
    const ok = formEl.checkValidity();
    if (!ok && typeof formEl.reportValidity === 'function') formEl.reportValidity();
    return ok;
  }

  return true;
}

export async function withBlockUI(fn, message = '<h3>Please wait, processing...</h3>') {
  const $ = window.jQuery || window.$;
  if (hasBlockUI()) $.blockUI({ message });
  try {
    return await fn();
  } finally {
    if (hasBlockUI()) $.unblockUI();
  }
}

/**
 * Call this whenever a view is ready (spapp route onReady)
 */
export function initValidationsForView(viewId) {
  // LOGIN
  if (viewId === 'login') {
    setupFormValidation('#loginForm',
      {
        email: { required: true, email: true },
        password: { required: true, minlength: 6, maxlength: 50 }
      },
      {
        email: {
          required: 'Email is required.',
          email: 'Please enter a valid email address.'
        },
        password: {
          required: 'Password is required.',
          minlength: 'Password must be at least 6 characters.',
          maxlength: 'Password is too long.'
        }
      }
    );
  }

  // REGISTER
  if (viewId === 'register') {
    setupFormValidation('#registerForm',
      {
        name: { required: true, minlength: 2, maxlength: 100 },
        email: { required: true, email: true },
        password: { required: true, minlength: 8, maxlength: 20 },
        confirm_password: { required: true, equalTo: '#registerPassword' }
      },
      {
        name: {
          required: 'Name is required.',
          minlength: 'Name must be at least 2 characters.',
          maxlength: 'Name is too long.'
        },
        email: {
          required: 'Email is required.',
          email: 'Please enter a valid email address.'
        },
        password: {
          required: 'Password is required.',
          minlength: 'Password must be at least 8 characters long.',
          maxlength: 'Password cannot be longer than 20 characters.'
        },
        confirm_password: {
          required: 'Please confirm your password.',
          equalTo: 'Passwords do not match.'
        }
      }
    );
  }

  // CONTACT
  if (viewId === 'contact') {
    setupFormValidation('#contactForm',
      {
        name: { required: true, minlength: 2, maxlength: 100 },
        email: { required: true, email: true },
        subject: { required: true, minlength: 3, maxlength: 120 },
        message: { required: true, minlength: 10, maxlength: 1000 }
      },
      {
        name: 'Please enter your full name.',
        email: {
          required: 'Email is required.',
          email: 'Please enter a valid email address.'
        },
        subject: 'Please enter a subject (min 3 characters).',
        message: 'Message must be at least 10 characters.'
      }
    );
  }

  // DASHBOARD forms (optional but recommended for M5)
  if (viewId === 'dashboard') {
    setupFormValidation('#projectForm',
      {
        title: { required: true, minlength: 3, maxlength: 120 },
        github_url: { url: true },
        project_url: { url: true },
        description: { maxlength: 300 }
      },
      {
        title: 'Project title is required (min 3 chars).',
        github_url: 'Please enter a valid URL.',
        project_url: 'Please enter a valid URL.',
        description: 'Description is too long.'
      }
    );

    setupFormValidation('#skillForm',
      {
        name: { required: true, minlength: 2, maxlength: 80 },
        proficiency: { required: true }
      },
      {
        name: 'Skill name is required.',
        proficiency: 'Please select proficiency.'
      }
    );

    setupFormValidation('#editProjectForm',
      {
        title: { required: true, minlength: 3, maxlength: 120 },
        github_url: { url: true },
        project_url: { url: true }
      },
      {
        title: 'Title is required.',
        github_url: 'Please enter a valid URL.',
        project_url: 'Please enter a valid URL.'
      }
    );

    setupFormValidation('#editSkillForm',
      {
        name: { required: true, minlength: 2, maxlength: 80 },
        proficiency: { required: true }
      },
      {
        name: 'Skill name is required.',
        proficiency: 'Please select proficiency.'
      }
    );
  }
}
