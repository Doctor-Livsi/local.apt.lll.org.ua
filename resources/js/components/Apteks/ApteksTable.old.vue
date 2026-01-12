<template>
  <div class="table-responsive">
    <table id="apteks-table" class="table table-sm table-striped table-hover w-100">
      <thead>
      <tr>
        <th>ID</th>
        <th>Назва</th>
        <th>IP</th>
        <th>Телефон</th>
        <th>Адреса</th>
      </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import DataTable from 'datatables.net-dt'
import 'datatables.net-responsive-dt'
import 'datatables.net-buttons-dt'
import 'datatables.net-select-dt'
import 'datatables.net-keytable-dt'
import 'datatables.net-colreorder-dt'
import 'datatables.net-searchpanes-dt'
import uk from 'datatables.net-plugins/i18n/uk.js'

const props = defineProps({ status: String })
axios.defaults.withCredentials = true

function formatPhone(data) {
  if (!data) return ''
  return data.replace(/<!--.*?-->/g, '').replace(/[:;]/g, '<br>')
}

onMounted(async () => {
  await axios.get('/sanctum/csrf-cookie')

  const filtersWrapper = document.createElement('div')
  filtersWrapper.classList.add('d-flex', 'align-items-center', 'mb-2', 'gap-2')

  filtersWrapper.innerHTML = `
    <select id="regionFilter" class="form-select form-select-sm me-2">
      <option value="">Всі області</option>
    </select>
    <select id="townFilter" class="form-select form-select-sm" disabled>
      <option value="">Спочатку оберіть область</option>
    </select>
  `

  const tableWrapper = document.querySelector('#apteks-table').closest('div')
  tableWrapper.parentNode.insertBefore(filtersWrapper, tableWrapper)

  // Заповнюємо області з API
  try {
    const res = await axios.get(`/api/regions/${props.status}`)
    const regionSelect = document.querySelector('#regionFilter')

    res.data.forEach(region => {
      regionSelect.innerHTML += `<option value="${region}">${region}</option>`
    })
  } catch (err) {
    console.error('❌ Не вдалося завантажити області', err)
  }

  const table = new DataTable('#apteks-table', {
    serverSide: true,
    processing: true,
    responsive: true,
    fixedHeader: true,
    pageLength: 10,
    order: [[1, 'asc']],
    language: uk,
    ajax: {
      url: `/api/apteks/${props.status}/data`,
      type: 'POST',
      dataType: 'json',
      xhrFields: {
        withCredentials: true
      },
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      data: function (d) {
        d.region = document.querySelector('#regionFilter').value
        d.town = document.querySelector('#townFilter').value
      },
      error(xhr, error, thrown) {
        console.error('🛑 DataTables AJAX Error', {
          status: xhr.status,
          statusText: xhr.statusText,
          responseText: xhr.responseText,
          error,
          thrown
        })

        alert(`Помилка завантаження даних (${xhr.status}): ${xhr.responseText.slice(0, 200)}...`)
      }
    },
    columns: [
      { data: 'id', width: '50px' },
      { data: 'name', width: '250px' },
      { data: 'apteka_ip', width: '130px' },
      {
        data: 'phone',
        render: formatPhone,
        width: '180px'
      },
      { data: 'address_full', width: 'auto' }
    ]
  })

  // Обробник регіону
  document.querySelector('#regionFilter').addEventListener('change', function () {
    const region = this.value
    const townSelect = document.querySelector('#townFilter')
    townSelect.innerHTML = ''

    if (region) {
      axios.get(`/api/towns/${props.status}`, { params: { region } })
          .then(res => {
            const towns = res.data
            townSelect.disabled = false
            townSelect.innerHTML = '<option value="">Всі міста</option>' +
                towns.map(town => `<option value="${town}">${town}</option>`).join('')
          })
          .catch(err => {
            console.error('❌ Не вдалося завантажити міста', err)
            townSelect.disabled = true
            townSelect.innerHTML = '<option value="">Міста не знайдені</option>'
          })
    } else {
      townSelect.disabled = true
      townSelect.innerHTML = '<option value="">Спочатку оберіть область</option>'
    }

    table.ajax.reload()
  })

  // Обробник міста
  document.querySelector('#townFilter').addEventListener('change', function () {
    table.ajax.reload()
  })
})
</script>
