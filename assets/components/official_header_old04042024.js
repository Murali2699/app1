class Header extends HTMLElement {
    constructor() {
      super();
    }
    connectedCallback() {

  this.innerHTML = `<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between">
    <i class="bi bi-list-nested toggle-sidebar-btn"></i>
    <a href="#" class="logo d-flex align-items-center mx-2">
      <img src="assets/img/tn_logo.png" alt="logo" class="logo_header">
      <span class="d-none d-lg-block">
        <!-- <i class="bi bi-globe"></i> -->
        Payment Automation Dashboard
      </span>
    </a>
  </div>
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center ">
      <li class="nav-item">
        <a href="#" onclick="logout();" class="nav-link mx-2 nav-profile" >
        <i class="bi bi-arrow-right-circle"></i> Log Out</a>
      </li>
    </ul>
  </nav>
  </header>
  <aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item" id="dashboard">
      <a class="nav-link official" href="dashboard-ui.html">
      <i class="bi bi-graph-up-arrow"></i>
      <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item" id="schemeRegistration">
      <a class="nav-link official" href="scheme.html">
      <i class="bi bi-folder-plus"></i>
      <span>Scheme Registration</span>
      </a>
    </li>
    <li class="nav-item" id="schemeList">
      <a class="nav-link official" href="scheme-list.html">
      <i class="bi bi-card-checklist"></i>
      <span>Scheme Registration List</span>
      </a>
    </li>
    <li class="nav-item" id="paymentProcess">
      <a class="nav-link official" href="payment-process.html">
      <i class="bi bi-list-columns"></i>
      <span>Payment Process</span>
      </a>
    </li>
    <li class="nav-item" id="paymentReport">
      <a class="nav-link official" href="all-report-new.html">
      <i class="bi bi-file-earmark-arrow-down"></i>
        <span>Reports</span>
      </a>
    </li>
     <li class="nav-item" id="paymentReport">
      <a class="nav-link official" href="rejected-report.html">
        <i class="bi bi-grid"></i>
        <span>Rejected / Reinitiate File Wise</span>
      </a>
    </li>
    <li class="nav-item" id="beneficiariesUpload">
      <a class="nav-link official" href="beneficiaries-upload.html">
      <i class="bi bi-cloud-upload"></i>
        <span>Beneficiaries upload</span>
      </a>
    </li>
    <!-- <li class="nav-item" id="importFileExport">
      <a class="nav-link official" href="file-import.html">
      <i class="bi bi-file-ruled"></i>
        <span>Beneficiaries File Import</span>
      </a>
    </li> -->
    <li class="nav-item" id="beneficiariesUpload">
      <a class="nav-link official" href="file-upload-details.html">
        <i class="bi bi-grid"></i>
        <span>File Upload Details</span>
      </a>
    </li>
    <li class="nav-item" id="masterData">
      <a class="nav-link official" href="master-data.html">
      <i class="bi bi-bar-chart"></i>
        <span>Master Data</span>
      </a>
    </li>
    <li class="nav-item" id="beneficiariesUpload">
      <a class="nav-link official" href="beneficiaries-search-ui.html">
      <i class="bi bi-cloud-upload"></i>
        <span>Beneficiaries Search</span>
      </a>
    </li>
    <!-- <li class="nav-item" id="user-master">
      <a class="nav-link official" href="reports.html">
      <i class="bi bi-card-checklist"></i>
      <span>Report</span>
      </a>
    </li> -->
    
  </ul>
  </aside>`;
    }
  }
  
  customElements.define('official-header-component', Header);



  // <li class="nav-item" id="beneficiariesUpload">
  //     <a class="nav-link official" href="dashboard-ui.html">
  //     <i class="bi bi-cloud-upload"></i>
  //       <span>Dashboard - <small><span class="blink badge badge-danger bg-danger">Coming Soon</span></small></span>
  //     </a>
  //   </li>
  //   <li class="nav-item" id="beneficiariesUpload">
  //     <a class="nav-link official" href="report-ui.html">
  //     <i class="bi bi-cloud-upload"></i>
  //       <span>Report - <small><span class="blink badge badge-danger bg-danger">Coming Soon</span></small></span>
  //     </a>
  //   </li>
   