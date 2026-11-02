'use strict';
document.querySelectorAll('[data-open]').forEach(button => button.addEventListener('click', () => document.getElementById(button.dataset.open).showModal()));
document.querySelectorAll('[data-close]').forEach(button => button.addEventListener('click', () => button.closest('dialog').close()));
document.querySelectorAll('dialog').forEach(dialog => {
 if (dialog.dataset.reopen === 'true') dialog.showModal();
 dialog.addEventListener('click', event => { if(event.target === dialog) { const r=dialog.getBoundingClientRect(); if(event.clientX<r.left || event.clientX>r.right || event.clientY<r.top || event.clientY>r.bottom) dialog.close(); } });
});
function filterTable(id) {
 const table=document.getElementById(id);
 const term=document.querySelector(`[data-search="${id}"]`).value.trim().toLocaleLowerCase();
 const category=id === 'expenses' ? document.getElementById('category-filter').value : '';
 let count=0,total=0;
 table.querySelectorAll('[data-row]').forEach(row=>{
  const show=row.textContent.toLocaleLowerCase().includes(term) && (!category || row.dataset.category === category);
  row.hidden=!show;
  if(show){count++;total+=Number(row.dataset.amount || 0);}
 });
 table.querySelector('[data-no-results]').hidden=count>0 || table.querySelectorAll('[data-row]').length===0;
 if(id === 'expenses') document.getElementById('filtered-total').textContent=`Showing ${count} entries · Filtered total: ৳ ${(total/100).toLocaleString('en-BD',{minimumFractionDigits:2,maximumFractionDigits:2})}. Dashboard totals include all entries.`;
}
document.querySelectorAll('[data-search]').forEach(input=>input.addEventListener('input',()=>filterTable(input.dataset.search)));
document.getElementById('category-filter').addEventListener('change',()=>filterTable('expenses'));
filterTable('expenses');
document.querySelectorAll('form').forEach(form=>form.addEventListener('submit',()=>{
 const button=form.querySelector('[type="submit"]');button.disabled=true;button.textContent='Saving…';
}));
