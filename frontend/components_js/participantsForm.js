export function renderParticipantsForm(onAdd) {
  const container = document.getElementById('participants-form');

  container.innerHTML = `

    <form id="participant-form">

      <label>
        Form of address *
        <select id="salutation" required>
          <option value="">-- Select --</option>
          <option value="formal">Formal</option>
          <option value="informal">Informal</option>
        </select>
      </label>


      <label>
        Title *
        <select id="title" required>
          <option value="">-- Select</option>
          <option>Mr.</option>
          <option>Ms.</option>
          <option>Dr.</option>
          <option>Prof.</option>
          <option>None</option>
        </select>
      </label>

      <label>
        First Name *
        <input type="text" id="firstName" required />
      </label>

      <label>
        Last Name *
        <input type="text" id="lastName" required />
      </label>

      <label>
        Company *
        <input type="text" id="company" requi
        ,red />
      </label>

      <label>
        E-mail *
        <input type="email" id="email" required />
      </label>

      <!-- Opcionais -->
      <label>
        Position/professional role
        <input type="text" id="role" />
      </label>

      <label>
        Company area
        <input type="text" id="area" />
      </label>

      <button type="submit">Register participant</button>
    </form>
  `;

  function sendConfirmationEmail(participant) {
    const templateParams = {
      salutation: participant.salutationText,
      title: participant.title,
      first_name: participant.firstName,
      full_name: hasTitle
      ? `${participant.title} ${participant.firstName} ${participant.lastName}`
      : `${participant.firstName} ${participant.lastName}`,
      company: participant.company,
      email: participant.email
    };
  
    return emailjs.send(
      'service_wea838f',
      'template_klw1jbx',
      templateParams
    );
  }
  

  const form = document.getElementById('participant-form');

  if (!form) return;
  
  form.addEventListener('submit', (e) => {
    e.preventDefault();
  
    const participant = {
      salutation: document.getElementById('salutation').value,
      title: document.getElementById('title').value,
      firstName: document.getElementById('firstName').value,
      lastName: document.getElementById('lastName').value,
      company: document.getElementById('company').value,
      email: document.getElementById('email').value,
      role: document.getElementById('role').value,
      area: document.getElementById('area').value
    };

    let finalSalutation = '';

    const hasTitle = participant.title && participant.title !== 'None';
    
    if (participant.salutation === 'formal') {
      finalSalutation = hasTitle
        ? `Dear ${participant.title} ${participant.lastName}`
        : `Dear ${participant.lastName}`;
    }
    
    if (participant.salutation === 'informal') {
      finalSalutation = `Dear ${participant.firstName} ${participant.lastName}`;
    }
    
    participant.salutationText = finalSalutation; 

    participant.salutationText = finalSalutation;


    fetch('_backoffice/insert_participants.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(participant)
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        alert(data.error || 'Failed to register participant');
        return;
      }
    
      // backend confirmou sucesso
      onAdd(participant);
      sendConfirmationEmail(participant);
      showSuccessState();
    
      const submitButton = form.querySelector('button[type="submit"]');
    
      submitButton.textContent = 'Register new Participant';
      submitButton.type = 'button';
    
      submitButton.addEventListener('click', () => {
        window.location.reload();
      });
    
      [...form.elements].forEach(el => {
        if (el !== submitButton) el.disabled = true;
      });
    })
    
  });
  
}
