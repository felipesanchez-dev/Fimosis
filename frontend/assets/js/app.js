// ✅ Versión funcional completa: guarda, busca por nombre, refresca y elimina seleccionados
(async function(){
  const URLS = {
    list: '../backend/students_list.php',
    search: '../backend/students_search.php',
    create: '../backend/students_create.php',
    update: '../backend/students_update.php',
    delete: '../backend/students_delete.php',
    batch_delete: '../backend/students_batch_delete.php',
    logout: '../backend/logout.php'
  };

  const tbody = document.querySelector('#studentsTable tbody');
  const searchInput = document.getElementById('search');
  const refreshBtn = document.getElementById('refresh');
  const newBtn = document.getElementById('newStudentBtn');
  const batchBtn = document.getElementById('batchDeleteBtn');
  const modal = document.getElementById('modal');
  const saveBtn = document.getElementById('saveBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const logoutBtn = document.getElementById('logoutBtn');
  const countEl = document.getElementById('count');
  const avgEl = document.getElementById('avg');

  let editingId = null;
  let currentList = [];

  async function fetchJson(url, options = {}) {
    options.credentials = 'include';
    if (!options.headers) options.headers = {};
    const res = await fetch(url, options);
    if (!res.ok) {
      console.error("Error HTTP:", res.status);
      return [];
    }
    try {
      return await res.json();
    } catch (e) {
      console.error("Error parseando JSON", e);
      return [];
    }
  }

  // ---- Cargar desde BD ----
  async function load(query = '') {
    // Si hay texto, buscar por nombre; si no, cargar todo
    const q = query.trim();
    const endpoint = q ? `${URLS.search}?q=${encodeURIComponent(q)}` : URLS.list;

    const data = await fetchJson(endpoint);
    currentList = Array.isArray(data) ? data : [];
    render(currentList);
  }

  // ---- Renderizar ----
  function render(list) {
    tbody.innerHTML = '';
    list.forEach(s => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input type="checkbox" class="sel" data-id="${s.id}"></td>
        <td>${s.id}</td>
        <td>${s.name}</td>
        <td>${s.grade ?? ''}</td>
        <td>${s.status ?? ''}</td>
        <td>
          <button class="btn ghost edit" data-id="${s.id}">Editar</button>
          <button class="btn ghost delete" data-id="${s.id}">Eliminar</button>
        </td>`;
      tbody.appendChild(tr);
    });
    countEl.textContent = list.length;
    const avg = list.length
      ? (list.reduce((a, b) => a + (parseFloat(b.grade) || 0), 0) / list.length).toFixed(2)
      : 0;
    avgEl.textContent = avg;
  }

  // ---- Buscar ----
  let timer = null;
  searchInput.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const q = searchInput.value.trim();
      load(q);
    }, 400);
  });

  // ---- Refrescar ----
  refreshBtn.addEventListener('click', () => load(searchInput.value.trim()));

  // ---- Nuevo ----
  newBtn.addEventListener('click', () => {
    editingId = null;
    document.getElementById('modalTitle').innerText = 'Nuevo estudiante';
    document.getElementById('mName').value = '';
    document.getElementById('mGrade').value = '';
    document.getElementById('mStatus').value = 'active';
    modal.style.display = 'flex';
  });

  cancelBtn.addEventListener('click', () => (modal.style.display = 'none'));

  // ---- Guardar (crear o actualizar) ----
  saveBtn.addEventListener('click', async () => {
    const name = document.getElementById('mName').value.trim();
    const grade = parseFloat(document.getElementById('mGrade').value) || 0;
    const status = document.getElementById('mStatus').value.trim() || 'active';
    if (!name) return alert('Nombre requerido');

    const data = { name, grade, status };
    if (editingId) data.id = editingId;

    const url = editingId ? URLS.update : URLS.create;
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(data)
    });

    if (res.ok) {
      modal.style.display = 'none';
      await load(searchInput.value.trim());
    } else {
      alert('Error guardando en la base de datos.');
    }
  });

  // ---- Editar o eliminar individual ----
  tbody.addEventListener('click', async e => {
    const el = e.target;
    if (el.classList.contains('edit')) {
      const id = el.dataset.id;
      const s = currentList.find(x => x.id == id);
      if (!s) return;
      editingId = id;
      document.getElementById('modalTitle').innerText = 'Editar estudiante';
      document.getElementById('mName').value = s.name;
      document.getElementById('mGrade').value = s.grade;
      document.getElementById('mStatus').value = s.status;
      modal.style.display = 'flex';
    }
    if (el.classList.contains('delete')) {
      const id = el.dataset.id;
      if (!confirm('¿Eliminar estudiante?')) return;
      await fetchJson(URLS.delete, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      await load(searchInput.value.trim());
    }
  });

  // ---- Eliminar seleccionados ----
  batchBtn.addEventListener('click', async () => {
    const ids = Array.from(document.querySelectorAll('.sel:checked'))
      .map(cb => parseInt(cb.dataset.id))
      .filter(id => !isNaN(id));

    if (!ids.length) return alert('Selecciona al menos uno.');
    if (!confirm('¿Eliminar los seleccionados?')) return;

    await fetchJson(URLS.batch_delete, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ids })
    });

    await load(searchInput.value.trim());
  });

  // ---- Logout ----
  logoutBtn.addEventListener('click', async () => {
    await fetchJson(URLS.logout, { method: 'POST' });
    window.location = 'login.html';
  });

  // ---- Cargar al inicio ----
  await load();
})();
