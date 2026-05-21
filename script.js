const WA='6281234567890';
const PRODS=[
  {id:1,cat:'meja',name:'Meja Makan Jati Solid',emoji:'🪵',price:'Rp 4.800.000',featured:true,desc:'Meja makan dari kayu jati pilihan grade A dengan finishing natural oil. Kokoh, tahan lama, dan semakin indah seiring usia. Tersedia dalam ukuran custom sesuai kebutuhan ruanganmu.',specs:[{l:'Bahan',v:'Kayu Jati A+'},{l:'Ukuran',v:'150×80×75 cm'},{l:'Finishing',v:'Natural Oil'},{l:'Kapasitas',v:'6 Kursi'}]},
  {id:2,cat:'kursi',name:'Kursi Tamu Minimalis',emoji:'🪑',price:'Rp 1.250.000',featured:false,desc:'Kursi tamu dengan desain minimalis modern berbahan kayu sungkai. Sandaran ergonomis untuk kenyamanan duduk yang lama. Cocok untuk ruang tamu, café, hingga co-working space.',specs:[{l:'Bahan',v:'Kayu Sungkai'},{l:'Ukuran',v:'48×52×82 cm'},{l:'Finishing',v:'Cat Duco'},{l:'Berat Max',v:'120 Kg'}]},
  {id:3,cat:'lemari',name:'Lemari Pakaian 3 Pintu',emoji:'🚪',price:'Rp 6.500.000',featured:false,desc:'Lemari pakaian 3 pintu dengan desain sliding modern. Interior dilengkapi gantungan baju, laci, dan rak sepatu. Material plywood premium lapis HPL anti-gores.',specs:[{l:'Bahan',v:'Plywood HPL'},{l:'Ukuran',v:'180×50×210 cm'},{l:'Pintu',v:'3 Sliding'},{l:'Interior',v:'Laci + Gantung'}]},
  {id:4,cat:'rak',name:'Rak Buku Kayu Walnut',emoji:'📚',price:'Rp 2.200.000',featured:false,desc:'Rak buku multi-fungsi dari kayu walnut asli dengan finishing wax polish. Bisa digunakan sebagai rak buku, display koleksi, atau divider ruangan. Tampilan mewah dan elegan.',specs:[{l:'Bahan',v:'Kayu Walnut'},{l:'Ukuran',v:'90×30×180 cm'},{l:'Finishing',v:'Wax Polish'},{l:'Lapisan',v:'5 Rak'}]},
  {id:5,cat:'sofa',name:'Sofa Rangka Kayu Scandinavian',emoji:'🛋️',price:'Rp 5.900.000',featured:false,desc:'Sofa 3 dudukan dengan rangka kayu jati bergaya Scandinavian. Busa high-density 35D untuk kenyamanan maksimal. Cover fabric premium anti-noda, mudah dilepas cuci.',specs:[{l:'Rangka',v:'Kayu Jati'},{l:'Ukuran',v:'190×80×82 cm'},{l:'Dudukan',v:'3 Orang'},{l:'Busa',v:'HD 35D'}]},
  {id:6,cat:'meja',name:'Meja Kerja Industrial',emoji:'🖥️',price:'Rp 2.750.000',featured:false,desc:'Meja kerja dengan kombinasi kayu solid dan rangka besi hitam bergaya industrial. Permukaan luas dengan 2 laci penyimpanan. Ideal untuk home office atau studio.',specs:[{l:'Top',v:'Kayu Pinus'},{l:'Rangka',v:'Besi Powder Coat'},{l:'Ukuran',v:'120×60×75 cm'},{l:'Laci',v:'2 Laci Kunci'}]},
  {id:7,cat:'kursi',name:'Kursi Goyang Klasik',emoji:'🪑',price:'Rp 1.800.000',featured:false,desc:'Kursi goyang klasik dari kayu mahoni pilihan. Desain timeless yang akan selalu relevan. Cocok untuk sudut baca, teras, atau area relaksasi. Tersedia dengan atau tanpa bantal.',specs:[{l:'Bahan',v:'Kayu Mahoni'},{l:'Ukuran',v:'60×80×105 cm'},{l:'Finishing',v:'Politur'},{l:'Berat',v:'12 Kg'}]},
  {id:8,cat:'rak',name:'Rak Dinding Floating',emoji:'🏺',price:'Rp 450.000',featured:false,desc:'Rak dinding floating dari kayu pinus natural. Tampilan bersih dan minimalis tanpa bracket terlihat. Mudah dipasang, kuat menahan hingga 15 Kg. Tersedia dalam 3 ukuran panjang.',specs:[{l:'Bahan',v:'Kayu Pinus'},{l:'Ukuran',v:'60/80/100×20 cm'},{l:'Beban Max',v:'15 Kg'},{l:'Finishing',v:'Clear Coat'}]}
];

function renderProds(f='all'){
  const g=document.getElementById('pgrid');
  g.innerHTML='';
  const list=f==='all'?PRODS:PRODS.filter(p=>p.cat===f);

  list.forEach((p,i)=>{
    const c=document.createElement('div');
    c.className='pc sr';
    c.innerHTML=`
      <div class="pc-img">
        <div class="pc-img-emoji">${p.emoji}</div>
        <div class="pc-veil"></div>
        <div class="pc-see">Lihat Detail</div>
      </div>
      <div class="pc-body">
        <div class="pc-cat">${p.cat}</div>
        <div class="pc-name">${p.name}</div>
        <div class="pc-price">${p.price}</div>
      </div>`;
    c.addEventListener('click',()=>openModal(p));
    g.appendChild(c);
    // staggered reveal
    requestAnimationFrame(()=>requestAnimationFrame(()=>{
      c.style.transitionDelay=(i*0.06)+'s';
      c.classList.add('vis');
      obs.observe(c);
    }));
  });
}

function openModal(p){
  document.getElementById('mCat').textContent=p.cat.toUpperCase();
  document.getElementById('mName').textContent=p.name;
  document.getElementById('mPrice').textContent=p.price;
  document.getElementById('mDesc').textContent=p.desc;
  document.getElementById('mImg').textContent=p.emoji;
  document.getElementById('mSpecs').innerHTML=p.specs.map(s=>`<div class="ms"><div class="ms-l">${s.l}</div><div class="ms-v">${s.v}</div></div>`).join('');
  const msg=encodeURIComponent(`Halo Ruang Kayu! Saya tertarik dengan *${p.name}* (${p.price}). Boleh minta info lebih lanjut?`);
  document.getElementById('mWa').href=`https://wa.me/${WA}?text=${msg}`;
  document.getElementById('moverlay').classList.add('on');
  document.body.style.overflow='hidden';
}
function closeModal(){document.getElementById('moverlay').classList.remove('on');document.body.style.overflow=''}
document.getElementById('mClose').addEventListener('click',closeModal);
document.getElementById('moverlay').addEventListener('click',e=>{if(e.target.id==='moverlay')closeModal()});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});

document.querySelectorAll('.pf').forEach(b=>{
  b.addEventListener('click',()=>{
    document.querySelectorAll('.pf').forEach(x=>x.classList.remove('on'));
    b.classList.add('on');
    renderProds(b.dataset.f);
  });
});

// Nav sticky
const navEl=document.getElementById('nav');
window.addEventListener('scroll',()=>navEl.classList.toggle('stuck',scrollY>80),{passive:true});

// Mobile menu
document.getElementById('hambBtn').addEventListener('click',()=>document.getElementById('mob').classList.add('on'));
document.getElementById('mobX').addEventListener('click',()=>document.getElementById('mob').classList.remove('on'));
function closeMob(){document.getElementById('mob').classList.remove('on')}

// Hero parallax on background image
const hBg=document.getElementById('hBgImg');
window.addEventListener('scroll',()=>{
  if(hBg) hBg.style.transform=`translateY(${window.scrollY*0.3}px)`;
},{passive:true});

// Scroll reveal
const obs=new IntersectionObserver(entries=>{
  entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('vis');obs.unobserve(e.target)}})
},{threshold:0.08});
document.querySelectorAll('.sr').forEach(el=>obs.observe(el));

// Cursor
const cur=document.getElementById('cur'),ring=document.getElementById('curing');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{
  mx=e.clientX;my=e.clientY;
  cur.style.left=mx+'px';cur.style.top=my+'px';
});
(function animRing(){
  rx+=(mx-rx)*0.09;ry+=(my-ry)*0.09;
  ring.style.left=rx+'px';ring.style.top=ry+'px';
  requestAnimationFrame(animRing);
})();
document.querySelectorAll('a,button,.pc,.f-btn,.pf').forEach(el=>{
  el.addEventListener('mouseenter',()=>{cur.classList.add('big');ring.classList.add('big')});
  el.addEventListener('mouseleave',()=>{cur.classList.remove('big');ring.classList.remove('big')});
});

renderProds();