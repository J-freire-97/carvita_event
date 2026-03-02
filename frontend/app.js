import { renderParticipantsForm } from './components_js/participantsForm.js';
import { renderParticipantsList } from './components_js/participantsList.js';

let participants = [];

function addParticipant(participant) {
  participants.push(participant);
  render();
}

function render() {
  renderParticipantsForm(addParticipant);
  renderParticipantsList(participants);
}

render();
