import { renderParticipantsForm } from './components_js/participantsForm.js';

function saveParticipant(participant) {
  const stored =
    JSON.parse(localStorage.getItem('participants')) || [];

  stored.push(participant);

  localStorage.setItem(
    'participants',
    JSON.stringify(stored)
  );

  document.getElementById('success-message').style.display = 'block';
}

renderParticipantsForm(saveParticipant);

function sendConfirmationEmail(participant) {
  const templateParams = {
    first_name: participant.firstName,
    full_name: `${participant.title} ${participant.firstName} ${participant.lastName}`,
    company: participant.company,
    email: participant.email
  };

  return emailjs.send(
    'service_wea838f',
    'template_klw1jbx',
    templateParams
  );
}
