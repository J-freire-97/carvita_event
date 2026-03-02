export function renderParticipantsList(participants) {
  const container = document.getElementById('participants-list');

  if (participants.length === 0) {
    container.innerHTML = '<p>No participants registered.</p>';
    return;
  }

  function formatName(p) {
    const title =
      p.title && p.title.toLowerCase() !== 'none'
        ? p.title
        : '';
  
    return `
      ${p.salutation}
      ${title}
      ${p.firstName}
      ${p.lastName}
    `.replace(/\s+/g, ' ').trim();
  }  

  container.innerHTML = `
    <ul>
      ${participants.map(p => `
        <li>
          <strong>${formatName(p)}</strong><br>
          ${p.company}<br>
          ${p.email}<br>
          ${p.role ? `${p.role}` : ''} ${p.area ? `– ${p.area}` : ''}<br><hr>
        </li>
      `).join('')}
    </ul>
  `;
}
