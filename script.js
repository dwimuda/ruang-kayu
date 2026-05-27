  const WA='628979227973';
  const DEFAULT_PRODS = [
    {
      id: 1,
      cat: 'lemari',
      name: 'Lemari Kayu Minimalis',
      imgs: ['/img/Lemari_1.jpg', '/img/Lemari_2.jpg'],
      price: 'Rp 1.450.000',
      desc: 'Lemari penyimpanan minimalis dari kayu pilihan dengan serat alami yang estetis. Desain multifungsi dilengkapi dengan rak terbuka di bagian atas dan pintu geser (sliding door) di bagian bawah. Kokoh, hemat tempat, dan sangat cocok untuk mempercantik ruangan bernuansa skandinavia atau industrial.',
      specs: [
        { l: 'Bahan', v: 'Kayu Jati A+' },
        { l: 'Ukuran', v: '80 x 60 x 35 cm (bisa custom)' },
        { l: 'Finishing', v: 'Natural Clear Gloss / Satin' },
        { l: 'Kapasitas', v: '1 Rak Terbuka, Pintu Geser, Sekat Dalam' },
      ],
    },
    {
      id: 2,
      cat: 'rak',
      name: 'Rak Serbaguna Kayu Minimalis',
      imgs: ['/img/Rak_1.jpg', '/img/Rak_2.jpg'],
      price: 'Rp 850.000',
      desc: 'Rak TV sekaligus rak display dengan desain asimetris bertingkat yang modern dan artistik. Terbuat dari kayu pilihan dengan guratan serat yang terekspos indah. Memiliki banyak sekat terbuka yang multifungsi untuk menaruh perangkat elektronik, buku, maupun dekorasi ruangan.',
      specs: [
        { l: 'Bahan', v: 'Kayu Sungkai' },
        { l: 'Ukuran', v: '120 x 35 x 60 cm (bisa custom)' },
        { l: 'Finishing', v: 'Natural Walnut / Dark Brown (Matte/Satin)' },
        { l: 'Desain', v: 'Asimetris Bertingkat' },
      ],
    },
    {
      id: 3,
      cat: ['meja', 'kursi'],
      name: 'Set Kursi Santai',
      imgs: ['/img/Set_Kursi_1.jpg', '/img/Set_Kursi_2.jpg'],
      price: 'Rp 1.250.000',
      desc: 'Set meja dan kursi outdoor/taman model minimalis rustic. Terbuat dari kayu palet pilihan yang tebal dan kokoh. Lapisan finishing rustic white memberikan kesan natural, unik, dan tahan terhadap cuaca luar ruangan. Cocok untuk pelengkap interior café, resto, taman, maupun area balkon.',
      specs: [
        { l: 'Bahan', v: 'Kayu Jati Belanda (Eks Palet Solid)' },
        { l: 'Ukuran', v: 'Meja 80x50x45cm, Kursi P 120cm' },
        { l: 'Finishing', v: 'Rustic Whitewash (Outdoor Coating)' },
        { l: 'Kapasitas', v: '2 Kursi Panjang, 1 Meja Utama' },
      ],
    },
    {
      id: 4,
      cat: ['meja', 'rak'],
      name: 'Meja Kerja dengan Rak Samping',
      imgs: ['/img/Meja_Rak_1.jpg', '/img/Meja_Rak_2.jpg'],
      price: 'Rp 1.850.000',
      desc: 'Solusi cerdas untuk ruang kerja atau kamar tidur minimalis. Meja kerja ini hadir dengan konsep industrial modern yang menyatu langsung dengan rak penyimpanan 4 tingkat di bagian samping. Sangat praktis untuk menaruh laptop, buku, tanaman hias, hingga perangkat elektronik kecil tanpa memakan banyak ruang.',
      specs: [
        { l: 'Bahan', v: 'Kayu Jati Belanda Solid' },
        { l: 'Rangka', v: 'Besi Hollow Finishing Hitam Doff' },
        { l: 'Ukuran', v: 'Total 120 x 60 x 140 cm (bisa custom)' },
        { l: 'Desain', v: 'Industrial Minimalis 4 Tingkat' },
      ],
    },
    {
      id: 5,
      cat: 'rak',
      name: 'Lemari Laci Minimalis 8 Susun',
      imgs: ['/img/Lemari_Laci.jpg'],
      price: 'Rp 2.100.000',
      desc: 'Maksimalkan penyimpanan pakaian dan barang Anda dengan lemari laci fungsional ini. Dilengkapi dengan total 8 laci besar yang simetris dan menggunakan tarikan (knob) minimalis. Desainnya yang modern dengan warna putih bertekstur serat kayu memberikan kesan bersih, rapi, dan luas pada kamar tidur atau ruang keluarga Anda.',
      specs: [
        { l: 'Bahan', v: 'Kayu Mahoni Oven & Multiplek 18mm' },
        { l: 'Ukuran', v: '140 x 45 x 85 cm' },
        { l: 'Kapasitas', v: '8 Laci Besar, Rel Laci Halus, Knob Minimalis' },
        { l: 'Finishing', v: 'Duco Putih Tulang (Broken White)' },
      ],
    },
  ];

  let PRODS = [...DEFAULT_PRODS];

  function getProductCategories(p) {
    if (Array.isArray(p.cat)) return p.cat;
    if (typeof p.cat === 'string') return p.cat.split(',').map(c => c.trim()).filter(Boolean);
    return [];
  }

  function getProductSpecs(p) {
    if (!p.specs || !Array.isArray(p.specs)) return [];
    return p.specs.map(s => {
      if (Array.isArray(s)) return { l: s[0] || '', v: s[1] || '' };
      return { l: s.l || '', v: s.v || '' };
    });
  };

  // ── CAROUSEL STATE ──────────────────────────────
  let curImgs = [], curIdx = 0;

  function buildCarousel(imgs) {
    curImgs = imgs; curIdx = 0;
    const wrap = document.getElementById('mImg');

    if (!imgs || imgs.length === 0) {
      wrap.innerHTML = '<span style="font-size:4rem">🪵</span>';
      return;
    }

    wrap.innerHTML = `
      <div class="mc-track" id="mcTrack">
        ${imgs.map((src, i) => `<img class="mc-slide" src="${src}" alt="foto ${i+1}" loading="${i===0?'eager':'lazy'}">`).join('')}
      </div>
      ${imgs.length > 1 ? `
      <button class="mc-btn mc-prev" id="mcPrev" aria-label="Sebelumnya">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
      <button class="mc-btn mc-next" id="mcNext" aria-label="Berikutnya">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
      </button>
      <div class="mc-dots" id="mcDots">
        ${imgs.map((_,i) => `<button class="mc-dot${i===0?' on':''}" data-i="${i}"></button>`).join('')}
      </div>` : ''}
      <div class="mc-counter" id="mcCounter">${imgs.length > 1 ? `1 / ${imgs.length}` : ''}</div>
    `;

    if (imgs.length > 1) {
      document.getElementById('mcPrev').addEventListener('click', e => { e.stopPropagation(); goSlide(curIdx - 1); });
      document.getElementById('mcNext').addEventListener('click', e => { e.stopPropagation(); goSlide(curIdx + 1); });
      document.querySelectorAll('.mc-dot').forEach(d => {
        d.addEventListener('click', e => { e.stopPropagation(); goSlide(+d.dataset.i); });
      });

      // Swipe support
      let tx = 0;
      wrap.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
      wrap.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - tx;
        if (Math.abs(dx) > 40) goSlide(curIdx + (dx < 0 ? 1 : -1));
      });
    }
  }

  function goSlide(n) {
    curIdx = (n + curImgs.length) % curImgs.length;
    const track = document.getElementById('mcTrack');
    if (track) track.style.transform = `translateX(-${curIdx * 100}%)`;
    document.querySelectorAll('.mc-dot').forEach((d, i) => d.classList.toggle('on', i === curIdx));
    const ctr = document.getElementById('mcCounter');
    if (ctr) ctr.textContent = `${curIdx + 1} / ${curImgs.length}`;
  }

  // ── MODAL ──────────────────────────────────────
  function renderProds(f='all'){
    const g=document.getElementById('pgrid');
    g.innerHTML='';
    const list=f==='all'?PRODS:PRODS.filter(p=>getProductCategories(p).includes(f));

    list.forEach((p,i)=>{
      const thumb = (p.imgs && p.imgs[0]) || '';
      const isImg = thumb && (thumb.startsWith('./') || thumb.startsWith('/') || thumb.startsWith('http'));
      const mediaTpl = isImg
        ? `<img src="${thumb}" alt="${p.name}" loading="lazy">`
        : `<div class="pc-img-emoji">${thumb}</div>`;
      const c=document.createElement('div');
      c.className='pc sr';
      c.innerHTML=`
        <div class="pc-img">
          ${mediaTpl}
          <div class="pc-veil"></div>
          <div class="pc-see">Lihat Detail</div>
        </div>
        <div class="pc-body">
          <div class="pc-cat">${getProductCategories(p).join(' · ')}</div>
          <div class="pc-name">${p.name}</div>
          <div class="pc-price">${p.price}</div>
        </div>`;
      c.addEventListener('click',()=>openModal(p));
      g.appendChild(c);
      requestAnimationFrame(()=>requestAnimationFrame(()=>{
        c.style.transitionDelay=(i*0.06)+'s';
        c.classList.add('vis');
        obs.observe(c);
      }));
    });
  }

  function openModal(p){
    document.getElementById('mCat').textContent=getProductCategories(p).join(' · ').toUpperCase();
    document.getElementById('mName').textContent=p.name;
    document.getElementById('mPrice').textContent=p.price;
    document.getElementById('mDesc').textContent=p.desc;
    buildCarousel(p.imgs || []);
    document.getElementById('mSpecs').innerHTML=getProductSpecs(p).map(s=>`<div class="ms"><div class="ms-l">${s.l}</div><div class="ms-v">${s.v}</div></div>`).join('');
    const msg=encodeURIComponent(`Halo Ruang Kayu! Saya tertarik dengan *${p.name}* (${p.price}). Boleh minta info lebih lanjut?`);
    document.getElementById('mWa').href=`https://wa.me/${WA}?text=${msg}`;
    document.getElementById('moverlay').classList.add('on');
    document.body.style.overflow='hidden';
  }
  function closeModal(){document.getElementById('moverlay').classList.remove('on');document.body.style.overflow=''}
  document.getElementById('mClose').addEventListener('click',closeModal);
  document.getElementById('moverlay').addEventListener('click',e=>{if(e.target.id==='moverlay')closeModal()});
  document.addEventListener('keydown',e=>{
    if(e.key==='Escape') closeModal();
    if(e.key==='ArrowLeft') goSlide(curIdx-1);
    if(e.key==='ArrowRight') goSlide(curIdx+1);
  });

  document.querySelectorAll('.pf').forEach(b=>{
    b.addEventListener('click',()=>{
      document.querySelectorAll('.pf').forEach(x=>x.classList.remove('on'));
      b.classList.add('on');
      renderProds(b.dataset.f);
    });
  });

  const navEl=document.getElementById('nav');
  window.addEventListener('scroll',()=>navEl.classList.toggle('stuck',scrollY>80),{passive:true});
  document.getElementById('hambBtn').addEventListener('click',()=>{
  const isOpen = document.getElementById('mob').classList.toggle('on');
  document.getElementById('hambBtn').classList.toggle('open', isOpen);
  });
  function closeMob(){
    document.getElementById('mob').classList.remove('on');
    document.getElementById('hambBtn').classList.remove('open');
  }
  const hBg=document.getElementById('hBgImg');
  window.addEventListener('scroll',()=>{if(hBg)hBg.style.transform=`translateY(${window.scrollY*0.3}px)`;},{passive:true});
  const obs=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('vis');obs.unobserve(e.target)}})},{threshold:0.08});
  document.querySelectorAll('.sr').forEach(el=>obs.observe(el));
  const cur=document.getElementById('cur'),ring=document.getElementById('curing');
  let mx=0,my=0,rx=0,ry=0;
  document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cur.style.left=mx+'px';cur.style.top=my+'px';});
  (function animRing(){rx+=(mx-rx)*0.09;ry+=(my-ry)*0.09;ring.style.left=rx+'px';ring.style.top=ry+'px';requestAnimationFrame(animRing);})();
  document.querySelectorAll('a,button,.pc,.f-btn,.pf').forEach(el=>{
    el.addEventListener('mouseenter',()=>{cur.classList.add('big');ring.classList.add('big')});
    el.addEventListener('mouseleave',()=>{cur.classList.remove('big');ring.classList.remove('big')});
  });

  async function init() {
    try {
      const res = await fetch('/api/index.php/products');
      if (res.ok) {
        PRODS = await res.json();
      }
    } catch (e) {
      console.warn("Could not load products from API, using fallback data:", e);
    }
    renderProds();
  }

  init();