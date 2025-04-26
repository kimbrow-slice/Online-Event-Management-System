async function postForm(url, formData) {
  const resp = await fetch(url, {
    method: 'POST',
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
  const resp = await fetch('events_data.php');
  const html = await resp.text();
  document.querySelector('#eventsTable tbody').innerHTML = html;
  hookEventButtons();
}

// Button click methods
function hookEventButtons() {
  document.querySelectorAll('.update-status-btn').forEach(btn => {
    btn.onclick = async () => {
      const id = btn.dataset.id;
      const status = prompt('New status?', btn.dataset.status);
      if (!status) return;
      const fd = new FormData();
      fd.set('event_id', id);
      fd.set('status', status);
      try {
        await postForm('update_event_status.php', fd);
        fetchEvents();
      } catch (e) { alert(e); }
    };
  });
  document.querySelectorAll('.delete-event-btn').forEach(btn => {
    btn.onclick = async () => {
      if (!confirm('Delete event?')) return;
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
  
}

// Helper for button
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
          "Registration successful! (SMTP would go here)";
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
if (
  window.location.pathname.includes('admin.html') &&
  window.currentUser &&
  ['admin', 'organizer'].includes(window.currentUser.type)
) {
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

// On page load, decide what to fetch
window.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('eventsTable')) {
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
}