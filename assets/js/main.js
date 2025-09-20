(function() {
  "use strict";

  
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }
  const on = (type, el, listener, all = false) => {
      select(el, all).forEach(e => e.addEventListener(type, listener))
      select(el, all).addEventListener(type, listener)
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  $(document).ready(function() {
    if ($('.toggle-sidebar-btn').length) {
      $('.toggle-sidebar-btn').on('click', function(e) {
        $('body').toggleClass('toggle-sidebar');
      });
  });
  if (select('.search-bar-toggle')) {
    on('click', '.search-bar-toggle', function(e) {
      select('.search-bar').classList.toggle('search-bar-show')
    })
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
        selectHeader.classList.remove('header-scrolled')
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
        backtotop.classList.add('active')
        backtotop.classList.remove('active')
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
   
})();
var pages = ['#dashboard', '#schemeRegistration', '#schemeList', '#paymentProcess', '#paymentReport', '#beneficiariesUpload'];
var schemeEnable = ['DDO','KMUT','PPS','OAP'];
function showPages() {
  pages.forEach(page => $(page).show());
}
function hidePages() {
  ['#schemeRegistration', '#schemeList'].forEach(page => {
    var href = $(page + ' a').attr('href');
    if (href && getCurrentPage() === href) {
      logoutWithPermission(page);
    $(page).hide();
function compareAndLogout(page) {
  $('.official').each(function (i, obj) {
    var href = obj.getAttribute("href");
    if (href == page) {
      logout();
function logoutWithPermission(page) {
  console.log('Logging out with permission for page:', page);
  logout();
function getCurrentPage() {
  var url = window.location.href;
  var page_url = url.split('/');
  var page = page_url[4];
  return page;
hidePages();
window.schemeEnable = ['DDO','KMUT','PPS','OAP'];
function roleBasedPageAllow(user_id,role_id){
  $.ajax({
    url: APIURL + 'user_based_schemes',
    type: "POST",
    headers: { 'X-APP-KEY': config.app_key, 'X-APP-NAME': config.app_name },
    data: { user_id: user_id },
    success: function (response) {
      
        if (role_id == 1 || role_id == 2) {
          showPages();
        } else {
          hidePages();
        }
        
    },
    error: function (xhr, status, error) {
      console.log("Error:", error);
function logout() {
  var cookies = document.cookie.split(";");
  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i];
    var eqPos = cookie.indexOf("=");
    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
  window.location.href = "login.html";