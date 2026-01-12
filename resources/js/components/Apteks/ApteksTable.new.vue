<template>
  <div id="app" class="card mb-2">
    <div class="card-body h-100">

      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <!-- Пошук -->
        <div class="search-input-container w-100 w-sm-auto">
          <input
              type="text"
              class="form-control datatable-search"
              placeholder="Пошук"
              data-datatable="#apteks-table"
          />
          <span class="search-magnifier-icon">
        <i data-acorn-icon="search"></i>
      </span>
          <span class="search-delete-icon d-none">
        <i data-acorn-icon="close"></i>
      </span>
        </div>

        <!-- Кнопки -->
        <div class="d-flex align-items-center gap-2 flex-wrap mt-3 mt-sm-0 justify-content-end w-100 w-sm-auto">
          <!-- Add New -->
          <button class="btn btn-icon btn-icon-only btn-outline-primary shadow add-datatable" type="button">
            <i data-acorn-icon="plus"></i>
          </button>
          <!-- Edit -->
          <button class="btn btn-icon btn-icon-only btn-outline-primary shadow edit-datatable" type="button">
            <i data-acorn-icon="edit"></i>
          </button>
          <!-- Delete -->
          <button class="btn btn-icon btn-icon-only btn-outline-primary shadow delete-datatable" type="button">
            <i data-acorn-icon="bin"></i>
          </button>
          <!-- Print -->
          <button class="btn btn-icon btn-icon-only btn-outline-primary shadow datatable-print" data-datatable="#apteks-table" type="button">
            <i data-acorn-icon="print"></i>
          </button>
          <!-- Export -->
          <div class="dropdown datatable-export" data-datatable="#apteks-table">
            <button class="btn btn-icon btn-icon-only btn-outline-primary shadow dropdown-toggle" data-bs-toggle="dropdown" type="button">
              <i data-acorn-icon="download"></i>
            </button>
            <ul class="dropdown-menu">
              <!--            <li><a class="dropdown-item export-copy" href="#">Copy</a></li>-->
              <li><a class="dropdown-item export-excel" href="#">Excel</a></li>
              <li><a class="dropdown-item export-cvs" href="#">CSV</a></li>
            </ul>
          </div>
          <!-- Page Size -->
          <div class="dropdown datatable-items-per-page" data-datatable="#apteks-table">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
              15 Items
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item active" href="#">15 Items</a></li>
              <li><a class="dropdown-item" href="#">25 Items</a></li>
              <li><a class="dropdown-item" href="#">50 Items</a></li>
            </ul>
          </div>
        </div>
      </div>


      <div class="table-responsive">
        <table id="apteks-table" class="table table-sm table-striped table-hover w-100">
          <thead>
          <tr>
            <th>ID</th>
            <th>Назва</th>
            <th>IP</th>
            <th>Телефон</th>
            <th>Адреса</th>
            <th><input type="checkbox" class="form-check-input" id="datatableCheckAll"></th>
          </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
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
  console.log('jQuery:', window.$);
  console.log('DataTable fn:', typeof $.fn.DataTable); // має бути "function"
  await axios.get('/sanctum/csrf-cookie')
  const filtersWrapper = document.createElement('div')
  const status = props.status
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
    const res = await axios.get(`/api/regions/${status}`)
    const regionSelect = document.querySelector('#regionFilter')

    res.data.forEach(region => {
      regionSelect.innerHTML += `<option value="${region}">${region}</option>`
    })
  } catch (err) {
    console.error('❌ Не вдалося завантажити області', err)
  }
  await new Promise(resolve => {
    const wait = () => $.fn.DataTable ? resolve() : setTimeout(wait, 100);
    wait();
  });

  const table = $('#apteks-table').DataTable({
    serverSide: true,
    processing: true,
    responsive: true,
    fixedHeader: true,
    searching: false,
    // paging: true,
    // pageLength: 10,
    dom: '<"row"<"col-sm-12"<"table-responsive"t>>>'
        + '<"row"<"col-12 mt-3 d-flex justify-content-center"p>>',
    pagingType: 'simple_numbers',
    language: uk,
    drawCallback() {
      window.Icon?.replace?.(); // для cs-chevron
    },

    ajax: {
      url: `/api/apteks/${status}/data`,
      type: 'POST',
      dataType: 'json',
      xhrFields: {
        withCredentials: true
      },
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      data: function (d) {
        d.region = document.querySelector('#regionFilter')?.value || ''
        d.town = document.querySelector('#townFilter')?.value || ''
      },
      error(xhr, error, thrown) {
        console.error('🛑 DataTables AJAX Error', { xhr, error, thrown })
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
      { data: 'address_full', width: 'auto' },
      {
        data: null,
        orderable: false,
        render: () => '<div class="form-check"><input type="checkbox" class="form-check-input"></div>',
        className: 'text-center',
        width: '30px'
      },
    ]
  })

  // Ініціалізація Acorn DatatableExtend
  new DatatableExtend({
    datatable: table,
    singleSelectCallback: () => console.log('🟩 One selected'),
    multipleSelectCallback: () => console.log('🟨 Many selected'),
    noneSelectCallback: () => console.log('⬜ None selected'),
    lengthChangeCallback: () => console.log('📄 Page length changed')
  })

  // Обробник регіону
  document.querySelector('#regionFilter').addEventListener('change', function () {
    const region = this.value
    const townSelect = document.querySelector('#townFilter')
    townSelect.innerHTML = ''

    if (region) {
      axios.get(`/api/towns/${status}`, { params: { region } })
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
