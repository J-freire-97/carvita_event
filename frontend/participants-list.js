import { renderParticipantsList } from './components_js/participantsList.js';

const participants =
  JSON.parse(localStorage.getItem('participants')) || [];

renderParticipantsList(participants);
