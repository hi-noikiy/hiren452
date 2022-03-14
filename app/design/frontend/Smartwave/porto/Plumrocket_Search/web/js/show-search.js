'use strict';
let searchBtn = document.querySelector('.pas-search-btn');
let searchForm = document.querySelector('#pas-mini-form'); 

searchBtn.addEventListener('click', function() {
    searchForm.classList.toggle('show-form');
})