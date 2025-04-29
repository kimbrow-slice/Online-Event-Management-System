let currentUser = { logged_in: false, user_type: 'guest' };

async function loadSession() {
  try {
    const res  = await fetch('get_session.php', { credentials: 'include' });
    const text = await res.text();

    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.warn('Invalid JSON from get_session.php:', text);
      data = { logged_in: false, user_type: 'guest' };
    }

    currentUser = {
      logged_in: data.logged_in,
      user_type: data.user_type || 'guest'
    };
  } catch (err) {
    console.error('loadSession failed entirely:', err);
    currentUser = { logged_in: false, user_type: 'guest' };
  }
}




async function postForm(url, formData) {
  const resp = await fetch(url, {
    method: 'POST',
    credentials: 'include', 
    body: formData
  });
  const text = await resp.text();
  if (!resp.ok) throw new Error(text);
  return text;
}

async function fetchFeedback() {
  const resp = await fetch('read_feedback.php');
  document.getElementById('feedbackList').innerHTML = await resp.text();
}



// CRUD for Events
async function fetchEvents() {
  const userType = currentUser.user_type || 'guest';
  const url      = userType === 'admin'
                   ? 'get_all_events.php'
                   : 'events_data.php';

  // load the rows
  const resp = await fetch(url, { credentials: 'include' });
  const html = await resp.text();
  document.querySelector('#eventsTable tbody').innerHTML = html;

  // wire up Edit/Delete for admins
  hookEventButtons();

  // wire up Attend buttons for everyone else
  document.querySelectorAll('.attend-btn').forEach(btn => {
    btn.onclick = () => {
      const row     = btn.closest('tr');
      const title   = row.querySelector('td:nth-child(4)').textContent;
      const date    = row.querySelector('td:nth-child(1)').textContent;
      const eventId = btn.dataset.id;

      document.getElementById('attendEventTitle').textContent       = title;
      document.getElementById('attendEventDate').textContent        = date;
      document.getElementById('attendEventId').value                = eventId;
      document.getElementById('guestName').value                    = '';
      document.getElementById('guestEmail').value                   = '';
      document.getElementById('emailConsent').checked               = false;
      document.getElementById('attendMsg').style.display            = 'none';

      $('#attendModal').modal('show');
    };
  });
}

// Edit events
document.querySelectorAll('.edit-event-btn').forEach(btn => {
  btn.onclick = async () => {
    const id = btn.dataset.id;
    try {
      const resp = await fetch(`get_single_event.php?event_id=${id}`, {
        credentials: 'include'
      });
      if (!resp.ok) throw new Error('Failed to load event.');
      const event = await resp.json();
  
      // populate modal
      document.getElementById('editEventId').value         = event.event_id;
      document.getElementById('editEventTitle').value      = event.title;
      document.getElementById('editEventDate').value       = event.event_date;
      document.getElementById('editEventTime').value       = event.event_time;
      document.getElementById('editEventLocation').value   = event.location;
      document.getElementById('editEventDescription').value= event.description;

  $('#editEventModal').modal('show');

    } catch (e) {
      alert(e.message);
    }
  };
});  

// Submit Updated Event
const editEventForm = document.getElementById('editEventForm');
if (editEventForm) {
  editEventForm.onsubmit = async (e) => {
    e.preventDefault();
    const fd = new FormData(editEventForm);

    try {
      await postForm('update_event.php', fd);  // We create this next
      $('#editEventModal').modal('hide');
      fetchEvents();
    } catch (err) {
      alert(err.message || "Failed to update event.");
    }
  };
}


// Fetch all registrations for admin page
async function fetchRegistrations() {
  const resp = await fetch('get_registrations.php', {
    credentials: 'include'
  });
  if (!resp.ok) {
    console.error('Failed to load registrations:', resp.status, await resp.text());
    return;
  }

  const data = await resp.json();
  console.log('Registrations payload:', data);

  const tbody = document.querySelector('#registrationsTable tbody');
  if (!tbody) return;

  tbody.innerHTML = '';
  data.forEach(reg => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${reg.registrant_name}</td>
      <td>${reg.registrant_email}</td>
      <td>${reg.event_title}</td>
      <td>${reg.registered_at}</td>
    `;
    tbody.appendChild(tr);
  });
}


window.addEventListener('DOMContentLoaded', async () => {
  // load session into currentUser
  await loadSession();
  
  if (
    currentUser.logged_in &&
    currentUser.user_type === 'admin' &&
    !window.location.pathname.endsWith('admin.html')
  ) {
    return window.location.href = 'admin.html';
  }
  // Bind registrations if on admin.html
  if (document.getElementById('registrationsTable')) {
    fetchRegistrations();
  }

  // After currentUser is set we can load events
  if (document.getElementById('eventsTable')) {
    fetchEvents();
  }
  // swap nav links
  if (currentUser.logged_in) {
    document.getElementById('loginNav').style.display  = 'none';
    document.getElementById('logoutNav').style.display = 'block';
  } else {
    document.getElementById('loginNav').style.display  = 'block';
    document.getElementById('logoutNav').style.display = 'none';
  }
});



// Button click methods
async function hookEventButtons() {
  document.querySelectorAll('.edit-event-btn').forEach(btn => {
    btn.onclick = async () => {
      const id = btn.dataset.id;
      console.log(' Editing event', id);

      // include credentials so SESSIONID is passed through
      const resp = await fetch(
        `get_single_event.php?event_id=${id}`,
        { credentials: 'include' }
      );

      console.log('status', resp.status);
      if (!resp.ok) throw new Error('Failed to load event');

      const event = await resp.json();
      // populate your modal fields...
      document.getElementById('editEventId').value          = event.event_id;
      document.getElementById('editEventTitle').value       = event.title;
      document.getElementById('editEventDate').value        = event.event_date;
      document.getElementById('editEventTime').value        = event.event_time;
      document.getElementById('editEventLocation').value    = event.location;
      document.getElementById('editEventDescription').value = event.description;

      $('#editEventModal').modal('show');
    };
  });
}


  document.querySelectorAll('.delete-event-btn').forEach(btn => {
    btn.onclick = async () => {
      if (!confirm('Deleting an Event is permanent. Are you sure?')) return;
      const fd = new FormData();
      fd.set('event_id', btn.dataset.id);
      try {
        await postForm('delete_event.php', fd);
        fetchEvents();
      } catch (e) { alert(e); }
    };
  });
  document.querySelectorAll('.attend-btn').forEach(btn => {
    btn.onclick = () => {
      const row = btn.closest('tr');
      const title = row.querySelector('td:nth-child(4)').textContent;
      const date = row.querySelector('td:nth-child(1)').textContent;
      const eventId = btn.dataset.id;
  
      document.getElementById('attendEventTitle').textContent = title;
      document.getElementById('attendEventDate').textContent = date;
      document.getElementById('attendEventId').value = eventId;
  
      document.getElementById('guestName').value = '';
      document.getElementById('guestEmail').value = '';
      document.getElementById('emailConsent').checked = false;
      document.getElementById('attendMsg').style.display = 'none';
  
      $('#attendModal').modal('show');
    };
  });


// Attend button
const attendForm = document.getElementById('attendForm');
if (attendForm) {
  attendForm.onsubmit = async (e) => {
    e.preventDefault();

    if (!document.getElementById('emailConsent').checked) {
      alert("You must agree to receive confirmation.");
      return;
    }

    const fd = new FormData(attendForm);

    try {
      const res = await fetch('submission.php', {
        method: 'POST',
        body: fd
      });

      const result = await res.json();

      if (res.ok) {
        document.getElementById('attendMsg').textContent =
          "Registration successful!";
        document.getElementById('attendMsg').style.display = 'block';
        attendForm.reset();
      } else {
        throw new Error(result.error || "Registration failed.");
      }
    } catch (err) {
      alert("Error: " + err.message);
    }
  };
}


// Protection for creating events restricted admin/organizer
if (window.location.pathname.includes('admin.html')) {
  const createEventForm = document.getElementById('createEventForm');
  if (createEventForm) {
    createEventForm.onsubmit = async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      try {
        await postForm('create_event.php', fd);
        e.target.reset();
        fetchEvents();
      } catch (err) {
        alert(err.message || "Failed to create event.");
      }
    };
  }
}



// Feedback
if (window.location.pathname.includes('feedback.html')) {
  const feedbackForm = document.getElementById('feedbackForm');
  if (feedbackForm) {
    feedbackForm.onsubmit = async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      try {
        await postForm('submit_feedback.php', fd);
        e.target.reset();
        fetchFeedback();
      } catch (err) {
        alert(err.message || "Failed to submit feedback.");
      }
    };
    fetchFeedback();
  }
}

  window.addEventListener('DOMContentLoaded', async () => {
    await loadSession();
  
    const isAdminPage = !!document.getElementById('editEventModal');
  
    if (isAdminPage) {
      if (currentUser.user_type !== 'admin') {
        alert("Access Denied: Admins Only.");
        window.location.href = "index.html";
        return;    
      }
      // admin sees full list
      fetchEvents();
    } else {
      // everyone else sees public list
      fetchEvents();
    }
  });
  



document.querySelectorAll('.attend-btn').forEach(btn => {
  btn.onclick = async () => {
    const eventId = btn.dataset.id;

    // Ask for guest email
    const guestEmail = prompt("Please enter your email to register:");
    if (!guestEmail || !/^\S+@\S+\.\S+$/.test(guestEmail)) {
      alert("Invalid email. Please try again.");
      return;
    }

    const fd = new FormData();
    fd.set('event_id', eventId);
    fd.set('guest_email', guestEmail);

    try {
      const response = await fetch('submission.php', {
        method: 'POST',
        body: fd
      });

      const result = await response.json();
      if (response.ok) {
        alert(result.message || 'Successfully registered!');
        btn.textContent === 'Attending';
        btn.disabled = true;
      } else {
        throw new Error(result.error);
      }
    } catch (err) {
      alert(" There was an issue registering you for this event " + err.message);
    }
  };
});


// Event Registration Page
if (window.location.pathname.includes('event_registration.html')) {
  const urlParams = new URLSearchParams(window.location.search);
  const eventId = urlParams.get('event_id');

  if (!eventId) {
    document.body.innerHTML = "<div class='container pt-5 text-center'><h3>Invalid event.</h3></div>";
  } else {
    const eventIdInput = document.getElementById('eventId');
    if (eventIdInput) eventIdInput.value = eventId;

    fetch(`get_event.php?event_id=${eventId}`)
      .then(res => res.json())
      .then(event => {
        document.getElementById('eventTitle').textContent = event.title;
        document.getElementById('eventDate').textContent = event.event_date;
        document.getElementById('eventTime').textContent = event.event_time;
        document.getElementById('eventLocation').textContent = event.location || 'TBD';
        document.getElementById('eventDescription').textContent = event.description;
      })
      .catch(() => {
        document.getElementById('eventTitle').textContent = "Event not found.";
      });

    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
      registrationForm.onsubmit = async (e) => {
        e.preventDefault();

        const email = document.getElementById('guestEmail').value;
        const consent = document.getElementById('consentCheckbox').checked;

        if (!consent) {
          alert("Please agree to receive a confirmation email.");
          return;
        }

        const fd = new FormData(e.target);

        const response = await fetch('submission.php', {
          method: 'POST',
          body: fd
        });

        const result = await response.json();

        if (response.ok) {
          const msg = document.getElementById('resultMessage');
          msg.style.display = 'block';
          msg.textContent = 'Registration successful! (SMTP email would be sent here)';
          registrationForm.reset();
        } else {
          alert(result.error || "An error occurred.");
        }
      };
    }
  }

const editEventForm = document.getElementById('editEventForm');
    if (editEventForm) {
      editEventForm.onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData(editEventForm);
        try {
          await postForm('update_event.php', fd);  // (we'll create update_event.php next)
          $('#editEventModal').modal('hide');
          fetchEvents();
        } catch (err) {
          alert(err.message || "Failed to update event.");
        }
      };
    }
}