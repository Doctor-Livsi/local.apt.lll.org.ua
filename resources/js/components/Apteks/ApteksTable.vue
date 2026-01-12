<template>
  <!-- Content Start -->
  <div class="data-table-boxed">
    <!-- Controls Start -->
    <div class="row mb-2">
      <!-- Search Start -->
      <div class="col-sm-12 col-md-5 col-lg-3 col-xxl-2 mb-1">
        <div class="d-inline-block float-md-start me-1 mb-1 search-input-container w-100 shadow bg-foreground">
          <input class="form-control datatable-search" placeholder="Пошук" data-datatable="#apteksTable">
          <span class="search-magnifier-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-search undefined"><circle cx="9" cy="9" r="7"></circle><path d="M14 14L17.5 17.5"></path></svg>
              </span>
          <span class="search-delete-icon d-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-close undefined"><path d="M5 5 15 15M15 5 5 15"></path></svg>
              </span>
        </div>
      </div>
      <!-- Search End -->

      <div class="col-sm-12 col-md-7 col-lg-9 col-xxl-10 text-end mb-1">
        <div class="d-inline-block me-0 me-sm-3 float-start float-md-none">
          <!-- Add Button Start -->
          <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow add-datatable" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" type="button" aria-label="Add" data-bs-original-title="Add">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
          </button>
          <!-- Add Button End -->

          <!-- Edit Button Start -->
          <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow edit-datatable disabled" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" type="button" aria-label="Edit" data-bs-original-title="Edit">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-edit undefined"><path d="M14.6264 2.54528C15.0872 2.08442 15.6782 1.79143 16.2693 1.73077C16.8604 1.67011 17.4032 1.84674 17.7783 2.22181C18.1533 2.59689 18.33 3.13967 18.2693 3.73077C18.2087 4.32186 17.9157 4.91284 17.4548 5.3737L6.53226 16.2962L2.22192 17.7782L3.70384 13.4678L14.6264 2.54528Z"></path></svg>
          </button>
          <!-- Edit Button End -->

          <!-- Delete Button Start -->
          <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow disabled delete-datatable" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" type="button" aria-label="Delete" data-bs-original-title="Delete">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-bin undefined"><path d="M4 5V14.5C4 15.9045 4 16.6067 4.33706 17.1111C4.48298 17.3295 4.67048 17.517 4.88886 17.6629C5.39331 18 6.09554 18 7.5 18H12.5C13.9045 18 14.6067 18 15.1111 17.6629C15.3295 17.517 15.517 17.3295 15.6629 17.1111C16 16.6067 16 15.9045 16 14.5V5"></path><path d="M14 5L13.9424 4.74074C13.6934 3.62043 13.569 3.06028 13.225 2.67266C13.0751 2.50368 12.8977 2.36133 12.7002 2.25164C12.2472 2 11.6734 2 10.5257 2L9.47427 2C8.32663 2 7.75281 2 7.29981 2.25164C7.10234 2.36133 6.92488 2.50368 6.77496 2.67266C6.43105 3.06028 6.30657 3.62044 6.05761 4.74074L6 5"></path><path d="M2 5H18M12 9V13M8 9V13"></path></svg>
          </button>
          <!-- Delete Button End -->
        </div>
        <div class="d-inline-block">
          <!-- Print Button Start -->
          <button class="btn btn-icon btn-icon-only btn-foreground-alternate shadow datatable-print" data-bs-delay="0" data-datatable="#apteksTable" data-bs-toggle="tooltip" data-bs-placement="top" type="button" aria-label="Print" data-bs-original-title="Print">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-print undefined"><path d="M6.44444 15 5.52949 15C4.13332 15 3.43524 15 2.9322 14.6657 2.71437 14.5209 2.52706 14.3348 2.38087 14.1179 2.04325 13.6171 2.03869 12.919 2.02956 11.5229L2.02302 10.5229C2.01379 9.1101 2.00917 8.40371 2.34565 7.89566 2.49128 7.67577 2.67897 7.48685 2.8979 7.33979 3.40374 7 4.11015 7 5.52295 7L14.477 7C15.8899 7 16.5963 7 17.1021 7.33979 17.321 7.48685 17.5087 7.67577 17.6543 7.89566 17.9908 8.40371 17.9862 9.1101 17.977 10.5229L17.9704 11.5229C17.9613 12.919 17.9568 13.6171 17.6191 14.1179 17.4729 14.3348 17.2856 14.5209 17.0678 14.6657 16.5648 15 15.8667 15 14.4705 15L13.5556 15M15 7 15 3.75C15 3.04777 15 2.69665 14.8315 2.44443 14.7585 2.33524 14.6648 2.24149 14.5556 2.16853 14.3033 2 13.9522 2 13.25 2L6.75 2C6.04777 2 5.69665 2 5.44443 2.16853 5.33524 2.24149 5.24149 2.33524 5.16853 2.44443 5 2.69665 5 3.04777 5 3.75L5 7"></path><path d="M12.25 13C12.9522 13 13.3033 13 13.5556 13.1685C13.6648 13.2415 13.7585 13.3352 13.8315 13.4444C14 13.6967 14 14.0478 14 14.75L14 16.25C14 16.9522 14 17.3033 13.8315 17.5556C13.7585 17.6648 13.6648 17.7585 13.5556 17.8315C13.3033 18 12.9522 18 12.25 18L7.75 18C7.04777 18 6.69665 18 6.44443 17.8315C6.33524 17.7585 6.24149 17.6648 6.16853 17.5556C6 17.3033 6 16.9522 6 16.25L6 14.75C6 14.0478 6 13.6967 6.16853 13.4444C6.24149 13.3352 6.33524 13.2415 6.44443 13.1685C6.69665 13 7.04777 13 7.75 13L12.25 13Z"></path><path d="M7 10H6H5"></path></svg>
          </button>
          <!-- Print Button End -->

          <!-- Export Dropdown Start -->
          <div class="d-inline-block datatable-export" data-datatable="#apteksTable">
            <button class="btn p-0" data-bs-toggle="dropdown" type="button" data-bs-offset="0,3">
                  <span class="btn btn-icon btn-icon-only btn-foreground-alternate shadow dropdown" data-bs-delay="0" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="Export" data-bs-original-title="Export">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-download undefined"><path d="M2 14V14.5C2 15.9045 2 16.6067 2.33706 17.1111C2.48298 17.3295 2.67048 17.517 2.88886 17.6629C3.39331 18 4.09554 18 5.5 18H14.5C15.9045 18 16.6067 18 17.1111 17.6629C17.3295 17.517 17.517 17.3295 17.6629 17.1111C18 16.6067 18 15.9045 18 14.5V14"></path><path d="M14 10 10.7071 13.2929C10.3166 13.6834 9.68342 13.6834 9.29289 13.2929L6 10M10 2 10 13"></path></svg>
                  </span>
            </button>
            <div class="dropdown-menu shadow dropdown-menu-end">
              <button class="dropdown-item export-copy" type="button">Copy</button>
              <button class="dropdown-item export-excel" type="button">Excel</button>
              <button class="dropdown-item export-cvs" type="button">Cvs</button>
            </div>
          </div>
          <!-- Export Dropdown End -->

          <!-- Length Start -->
          <div class="dropdown-as-select d-inline-block datatable-length" data-datatable="#apteksTable" data-childselector="span">
            <button class="btn p-0 shadow" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-offset="0,3">
              <span class="btn btn-foreground-alternate dropdown-toggle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-delay="0" data-bs-original-title="Item Count">15 Items</span>
            </button>
            <div class="dropdown-menu shadow dropdown-menu-end">
              <a class="dropdown-item active" href="#">10 Items</a>
              <a class="dropdown-item" href="#">15 Items</a>
              <a class="dropdown-item" href="#">20 Items</a>
            </div>
          </div>
          <!-- Length End -->
        </div>
      </div>
    </div>
    <!-- Controls End -->
    <!-- Пример: под поиском -->
    <div class="row mb-2">
      <div class="col-sm-12 col-md-5 col-lg-3 col-xxl-2 mb-1">
        <div class="d-flex gap-2">
          <div class="btn-group me-1">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="regionDropdownBtn">
              Всі області
            </button>
            <ul class="dropdown-menu" id="regionDropdown"></ul>
          </div>
          <div class="btn-group">
            <button class="btn btn-outline-secondary dropdown-toggle disabled" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="townDropdownBtn">
              Спочатку оберіть область
            </button>
            <ul class="dropdown-menu" id="townDropdown"></ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Table Start -->
    <div>
      <div id="apteksTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
        <div class="row">
          <div class="col-sm-12">
            <div class="table-container">
              <div class="card" style="height: 710px;">
                <div class="card-body half-padding">
                  <table id="apteksTable" class="data-table nowrap hover dataTable no-footer" role="grid" style="width: 1795px;">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>IP</th>
                        <th>Телефон</th>
                        <th>Адреса</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
<!--        <div class="row">-->
<!--          <div class="col-12 mt-3">-->
<!--            <div class="dataTables_paginate paging_simple_numbers" id="apteksTable_paginate">-->
<!--              <ul class="pagination">-->
<!--                <li class="paginate_button page-item previous disabled" id="apteksTable_previous">-->
<!--                  <a href="#" aria-controls="apteksTableRows" data-dt-idx="0" tabindex="0" class="page-link">-->
<!--                  <i class="cs-chevron-left"></i></a></li><li class="paginate_button page-item active">-->
<!--                  <a href="#" aria-controls="apteksTableRows" data-dt-idx="1" tabindex="0" class="page-link">1</a>-->
<!--                </li>-->
<!--                <li class="paginate_button page-item ">-->
<!--                  <a href="#" aria-controls="apteksTableRows" data-dt-idx="2" tabindex="0" class="page-link">2</a>-->
<!--                </li>-->
<!--                <li class="paginate_button page-item next" id="apteksTable_next">-->
<!--                  <a href="#" aria-controls="apteksTableRows" data-dt-idx="3" tabindex="0" class="page-link">-->
<!--                  <i class="cs-chevron-right"></i>-->
<!--                  </a>-->
<!--                </li>-->
<!--              </ul>-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->
      </div>
    </div>
    <!-- Table End -->
    <div class="modal modal-right fade" id="addEditModal" tabindex="-1" aria-labelledby="modalTitle" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Edit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="Name" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Sales</label>
                <input name="Sales" type="number" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Stock</label>
                <input name="Stock" type="number" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Category</label>
                <div class="form-check">
                  <input type="radio" id="category1" name="Category" value="Whole Wheat" class="form-check-input">
                  <label class="form-check-label" for="category1">Whole Wheat</label>
                </div>
                <div class="form-check">
                  <input type="radio" id="category2" name="Category" value="Sourdough" class="form-check-input">
                  <label class="form-check-label" for="category2">Sourdough</label>
                </div>
                <div class="form-check">
                  <input type="radio" id="category3" name="Category" value="Multigrain" class="form-check-input">
                  <label class="form-check-label" for="category3">Multigrain</label>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Tag</label>
                <div class="form-check">
                  <input type="radio" id="tag1" name="Tag" value="New" class="form-check-input">
                  <label class="form-check-label" for="tag1">New</label>
                </div>
                <div class="form-check">
                  <input type="radio" id="tag2" name="Tag" value="Sale" class="form-check-input">
                  <label class="form-check-label" for="tag2">Sale</label>
                </div>
                <div class="form-check">
                  <input type="radio" id="tag3" name="Tag" value="Done" class="form-check-input">
                  <label class="form-check-label" for="tag3">Done</label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="addEditConfirmButton">Done</button>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-dt';
import 'datatables.net-autofill-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-colreorder-dt';
import 'datatables.net-keytable-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import DatatableExtend from '@/cs/datatable.extend.js';
// import EditableBoxed from '@/plugins/datatable.editableboxed.js';
// import Script from '@/script.js';
console.log('DataTable:', DataTable);
// DataTables.Buttons.jszip(jszip);
// DataTables.Buttons.pdfMake(pdfmake);
// DataTable.use(DataTablesCore);

import uk from 'datatables.net-plugins/i18n/uk.js';
// import 'mousetrap';

const props = defineProps({ status: String })
axios.defaults.withCredentials = true

function populateDropdown(dropdownEl, btnEl, items, placeholder = 'Оберіть') {
  dropdownEl.innerHTML = ''
  btnEl.textContent = placeholder
  btnEl.dataset.value = ''

  // 🔥 Додаємо "Всі області" в початок
  const defaultLi = document.createElement('li')
  const defaultA = document.createElement('a')
  defaultA.className = 'dropdown-item'
  defaultA.href = '#'
  defaultA.textContent = placeholder
  defaultA.onclick = (e) => {
    e.preventDefault()
    btnEl.textContent = placeholder
    btnEl.dataset.value = ''
    dropdownEl.dispatchEvent(new Event('change'))
  }
  defaultLi.appendChild(defaultA)
  dropdownEl.appendChild(defaultLi)

  // 🔁 Потім додаємо всі області
  items.forEach(item => {
    const li = document.createElement('li')
    const a = document.createElement('a')
    a.className = 'dropdown-item'
    a.href = '#'
    a.textContent = item
    a.onclick = (e) => {
      e.preventDefault()
      btnEl.textContent = item
      btnEl.dataset.value = item
      dropdownEl.dispatchEvent(new Event('change'))
    }
    li.appendChild(a)
    dropdownEl.appendChild(li)
  })
}


function formatPhone(data) {
  if (!data) return ''
  return data.replace(/<!--.*?-->/g, '').replace(/[:;,]/g, '<br>')
}
console.log('🔥 status:', props.status)

onMounted(async () => {
  await axios.get('/sanctum/csrf-cookie')
 
  const regionSelect = document.querySelector('#regionFilter')
  const townSelect = document.querySelector('#townFilter')

  // const tableWrapper = document.querySelector('#apteksTable').closest('div')

  // Заповнюємо області з API
  try {
    const res = await axios.get(`/api/regions/${props.status}`)
    populateDropdown(regionDropdown, regionDropdownBtn, res.data, 'Всі області')
  } catch (err) {
    console.error('❌ Не вдалося завантажити області', err)
  }

  const table = new DataTable('#apteksTable', {
    serverSide: true,
    processing: true,
    responsive: true,
    fixedHeader: true,
    
    // paging: true,
    // paginate: true,
    pageLength: 10,
    order: [[1, 'asc']],
    // language: uk,
    dom: '<"table-responsive"t>',

    pagingType: 'simple_numbers',
    language: uk,
    drawCallback() {
      window.Icon?.replace?.(); // для cs-chevron
    },

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
        d.region = document.getElementById('regionDropdownBtn')?.dataset?.value || ''
        d.town = document.getElementById('townDropdownBtn')?.dataset?.value || ''
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
  regionDropdown.addEventListener('change', async () => {
    const region = regionDropdownBtn.dataset.value

    if (!region) {
      townDropdownBtn.textContent = 'Спочатку оберіть область'
      townDropdownBtn.classList.add('disabled')
      townDropdown.innerHTML = ''
      table.ajax.reload()
      return
    }

    try {
      const res = await axios.get(`/api/towns/${props.status}`, { params: { region } })
      populateDropdown(townDropdown, townDropdownBtn, res.data, 'Всі міста')
      townDropdownBtn.classList.remove('disabled')
    } catch {
      townDropdownBtn.textContent = 'Міста не знайдені'
      townDropdownBtn.classList.add('disabled')
      townDropdown.innerHTML = ''
    }

    table.ajax.reload()
  })

  townDropdown.addEventListener('change', () => {
    table.ajax.reload()
  })

})
</script>
