const WA='6281234567890';
const PRODS = [
  {
    id: 1,
    cat: 'lemari',
    name: 'Lemari Kayu Minimalis',
    img: '/img/Lemari_1.jpg',
    price: 'Rp -',
    desc: 'Lemari penyimpanan minimalis dari kayu pilihan dengan serat alami yang estetis. Desain multifungsi dilengkapi dengan rak terbuka di bagian atas dan pintu geser (sliding door) di bagian bawah. Kokoh, hemat tempat, dan sangat cocok untuk mempercantik ruangan bernuansa skandinavia atau industrial.',
    specs: [
      { l: 'Bahan',     v: 'Kayu Jati A+' },
      { l: 'Ukuran',    v: ' 80 x 60 x 35 cm (bisa custom)' },
      { l: 'Finishing', v: 'Natural Clear Gloss / Satin (Menonjolkan serat asli kayu)'   },
      { l: 'Kapasitas', v: '1 Rak Terbuka, Pintu Geser (Sliding Door), Sekat Dalam'       },
    ],
  },
  {
    id: 2,
    cat: 'rak',
    name: 'Rak Serbaguna Kayu Minimalis',
    img: '/img/Rak_1.jpg',
    price: 'Rp -',
    desc: 'Rak TV sekaligus rak display dengan desain asimetris bertingkat yang modern dan artistik. Terbuat dari kayu - pilihan dengan guratan serat yang terekspos indah. Memiliki banyak sekat terbuka yang multifungsi untuk menaruh perangkat elektronik, buku, maupun dekorasi ruangan.',
    specs: [
      { l: 'Bahan',     v: 'Kayu Sungkai' },
      { l: 'Ukuran',    v: '120 x 35 x 60 cm (bisa custom)'  },
      { l: 'Finishing', v: 'Natural Walnut / Dark Brown (Matte/Satin)'     },
      { l: 'Desain', v: 'Asimetris Bertingkat'       },
    ],
  },
  {
    id: 3,
    cat: ['meja', 'kursi'],
    name: 'Set kursi santai',
    img: '/img/Set_Kursi_1.jpg',
    price: 'Rp -',
    desc: 'Set meja dan kursi outdoor/taman model minimalis rustic. Terbuat dari kayu palet pilihan yang tebal dan kokoh. Lapisan finishing rustic white memberikan kesan natural, unik, dan tahan terhadap cuaca luar ruangan. Cocok untuk pelengkap interior café, resto, taman, maupun area balkon.',
    specs: [
      { l: 'Bahan',    v: '-'  },
      { l: 'Ukuran',   v: '-'},
      { l: 'Finishing',    v: '-'    },
      { l: 'Design', v: '-'},
    ],
  },
  {
    id: 4,
    cat: ['meja', 'rak'],
    name: 'Meja kerja dengan rak samping',
    img: '/img/Meja_Rak_1.jpg',
    price: 'Rp -',
    desc: 'Solusi cerdas untuk ruang kerja atau kamar tidur minimalis. Meja kerja ini hadir dengan konsep industrial modern yang menyatu langsung dengan rak penyimpanan 4 tingkat di bagian samping. Sangat praktis untuk menaruh laptop, buku, tanaman hias, hingga perangkat elektronik kecil tanpa memakan banyak ruang.',
    specs: [
      { l: 'Bahan',     v: 'Kayu Jati Belanda' },
      { l: 'Rangka',   v: 'Besi finishing hitam doff'    },
      { l: 'Ukuran',    v: '(bisa custom)'  },
      { l: 'Desain',   v: 'Industrial minimalis'       },
    ],
  },
  {
    id: 5,
    cat: 'rak',
    name: 'Lemari Laci Minimalis 8 Susun',
    img: '/img/Lemari_Laci.jpg',
    price: 'Rp -',
    desc: 'Maksimalkan penyimpanan pakaian dan barang Anda dengan Lemari Laci Minimalis 4 Tingkat ini. Dilengkapi dengan total 8 laci besar yang simetris dan menggunakan tarikan (knob) minimalis. Desainnya yang modern dengan warna putih bertekstur serat kayu memberikan kesan bersih, rapi, dan luas pada kamar tidur atau ruang keluarga Anda.',
    specs: [
      { l: 'Bahan',   v: '-'    },
      { l: 'Ukuran',   v: '-' },
      { l: 'Kapasitas',  v: '8 Laci Besar, Rel Laci Halus, Knob Stainless Minimalis'      },
      { l: 'Desain',     v: 'Industrial minimalis'       },
    ],
  },
 
];

function renderProds(f='all'){
  const g=document.getElementById('pgrid');
  g.innerHTML='';
  const list=f==='all'?PRODS:PRODS.filter(p=>[].concat(p.cat).includes(f));

  list.forEach((p,i)=>{
    const c=document.createElement('div');
    const isImg = p.img && (p.img.startsWith('./') || p.img.startsWith('/') || p.img.startsWith('http'));
    const mediaTpl = isImg
      ? `<img src="${p.img}" alt="${p.name}" loading="lazy">`
      : `<div class="pc-img-emoji">${p.img}</div>`;
    c.className='pc sr';
    c.innerHTML=`
      <div class="pc-img">
        ${mediaTpl}
        <div class="pc-veil"></div>
        <div class="pc-see">Lihat Detail</div>
      </div>
      <div class="pc-body">
        <div class="pc-cat">${[].concat(p.cat).join(' · ')}</div>
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
  document.getElementById('mCat').textContent=[].concat(p.cat).join(' · ').toUpperCase();
  document.getElementById('mName').textContent=p.name;
  document.getElementById('mPrice').textContent=p.price;
  document.getElementById('mDesc').textContent=p.desc;
  const mImgEl=document.getElementById('mImg');
  const isImgM=p.img&&(p.img.startsWith('./')||p.img.startsWith('/')||p.img.startsWith('http'));
  if(isImgM){mImgEl.innerHTML=`<img src="${p.img}" alt="${p.name}">`;mImgEl.style.fontSize=''}
  else{mImgEl.innerHTML='';mImgEl.textContent=p.img;mImgEl.style.fontSize='7rem'}
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