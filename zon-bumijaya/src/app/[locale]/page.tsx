"use client";

import { useTranslations } from 'next-intl';
import Image from 'next/image';
import { Link } from '@/i18n/navigation';
import { useState, useRef, useEffect } from 'react';
import { ChevronDown, MessageCircle } from 'lucide-react';
import { ThemeToggle } from '@/components/ThemeToggle';

import gsap from 'gsap';
import { useGSAP } from '@gsap/react';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Magnetic Button using GSAP
const MagneticButton = ({ children, className }: { children: React.ReactNode, className?: string }) => {
  const ref = useRef<HTMLButtonElement>(null);
  
  const { contextSafe } = useGSAP();

  const handleMouse = contextSafe((e: React.MouseEvent<HTMLButtonElement>) => {
    if (!ref.current) return;
    const { clientX, clientY } = e;
    const { height, width, left, top } = ref.current.getBoundingClientRect();
    const middleX = clientX - (left + width / 2);
    const middleY = clientY - (top + height / 2);
    
    gsap.to(ref.current, {
      x: middleX * 0.2,
      y: middleY * 0.2,
      duration: 1,
      ease: 'power3.out'
    });
  });

  const reset = contextSafe(() => {
    if (!ref.current) return;
    gsap.to(ref.current, {
      x: 0,
      y: 0,
      duration: 1,
      ease: 'elastic.out(1, 0.3)'
    });
  });

  return (
    <button
      ref={ref}
      onMouseMove={handleMouse}
      onMouseLeave={reset}
      className={className}
    >
      {children}
    </button>
  );
};

export default function HomePage() {
  const t = useTranslations('Index');
  const [scrolled, setScrolled] = useState(false);
  const [activeFaq, setActiveFaq] = useState<number | null>(null);
  const [products, setProducts] = useState<any[]>([
    // Fallback data while fetching
    { title: 'Solid Wood Pallet', img: '/solid_pallet.png', desc: 'Heavy-duty industrial logistics.', id: '1', sku: 'PALLET-001' },
    { title: 'Laminated Wood', img: '/laminated_wood.png', desc: 'Engineered structural integrity.', id: '2', sku: 'LVL-BEAM-001' },
    { title: 'Finger Joint', img: '/finger_joint.png', desc: 'Flawless interlocking strength.', id: '3', sku: 'FINGER-JOINT-001' }
  ]);

  const containerRef = useRef<HTMLDivElement>(null);
  const horizontalScrollRef = useRef<HTMLDivElement>(null);
  const horizontalWrapRef = useRef<HTMLDivElement>(null);
  const heroImageRef = useRef<HTMLImageElement>(null);

  useGSAP(() => {
    // Hero Parallax
    if (heroImageRef.current) {
      gsap.to(heroImageRef.current, {
        y: '20%',
        ease: 'none',
        scrollTrigger: {
          trigger: containerRef.current,
          start: 'top top',
          end: 'bottom top',
          scrub: true,
        },
      });
    }

    // Hero Text Reveal
    const tl = gsap.timeline();
    tl.fromTo('.hero-text-line', {
      y: 100,
      opacity: 0,
      rotateX: -30
    }, {
      y: 0,
      opacity: 1,
      rotateX: 0,
      duration: 1.2,
      stagger: 0.2,
      ease: 'power4.out',
      delay: 0.5
    });

    tl.fromTo('.hero-badge', { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 1 }, "-=1");
    tl.fromTo('.hero-button', { opacity: 0, scale: 0.8 }, { opacity: 1, scale: 1, duration: 1, ease: 'back.out(1.7)' }, "-=0.8");

    // Horizontal Scroll (Conveyor Belt)
    if (horizontalScrollRef.current && horizontalWrapRef.current) {
      const getScrollAmount = () => {
        let wrapWidth = horizontalWrapRef.current?.scrollWidth || 0;
        return -(wrapWidth - window.innerWidth);
      };

      const tween = gsap.to(horizontalWrapRef.current, {
        x: getScrollAmount,
        ease: 'none',
      });

      ScrollTrigger.create({
        trigger: horizontalScrollRef.current,
        start: 'top top',
        end: () => `+=${getScrollAmount() * -1}`,
        pin: true,
        animation: tween,
        scrub: 1,
        invalidateOnRefresh: true,
      });
    }

    // Reveal elements on scroll
    gsap.utils.toArray('.reveal-up').forEach((elem: any) => {
      gsap.fromTo(elem, 
        { y: 100, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: elem,
            start: 'top 85%',
          }
        }
      );
    });

    gsap.utils.toArray('.reveal-image').forEach((elem: any) => {
      gsap.fromTo(elem, 
        { clipPath: 'inset(100% 0 0 0)' },
        {
          clipPath: 'inset(0% 0 0 0)',
          duration: 1.5,
          ease: 'power4.inOut',
          scrollTrigger: {
            trigger: elem,
            start: 'top 80%',
          }
        }
      );
    });

  }, { scope: containerRef });

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 50);
    window.addEventListener('scroll', handleScroll);

    // Attempt to fetch live products — silently fall back to mock data if backend is unavailable
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 3000);

    fetch('http://localhost:8000/jsonapi/product?include=media,text', { signal: controller.signal })
      .then(res => {
        clearTimeout(timeout);
        return res.json();
      })
      .then(data => {
        if (data && data.data) {
          const liveProducts = data.data.map((item: any) => {
            const mediaIds = item.relationships?.media?.data?.map((m: any) => m.id) || [];
            const textIds = item.relationships?.text?.data?.map((t: any) => t.id) || [];

            let img = '/hero_timber.png';
            let desc = 'Premium Industrial Timber';

            if (data.included) {
              const media = data.included.find((inc: any) => inc.type === 'media' && mediaIds.includes(inc.id));
              if (media?.attributes) img = media.attributes['media.url'];

              const text = data.included.find((inc: any) => inc.type === 'text' && textIds.includes(inc.id));
              if (text?.attributes) desc = text.attributes['text.content'];
            }

            return { id: item.id, title: item.attributes['product.label'], desc, img, sku: 'product-' + item.id };
          });
          if (liveProducts.length > 0) setProducts(liveProducts);
        }
      })
      .catch(() => {
        // Backend offline — mock data already displayed, nothing to do
        clearTimeout(timeout);
      });

    return () => {
      window.removeEventListener('scroll', handleScroll);
      controller.abort();
    };
  }, []);

  const toggleFaq = (i: number) => {
    if (activeFaq === i) {
      setActiveFaq(null);
    } else {
      setActiveFaq(i);
    }
  };

  return (
    <div ref={containerRef} className="flex flex-col min-h-screen bg-background text-on-background overflow-hidden selection:bg-primary selection:text-on-primary">
      {/* Sticky Header */}
      <header className={`fixed top-0 w-full z-50 transition-all duration-500 ${scrolled ? 'bg-background/80 backdrop-blur-xl border-b border-on-background/5 py-4' : 'bg-transparent py-8'}`}>
        <div className="max-w-container-max mx-auto px-gutter flex justify-between items-center">
          <div className="font-display text-primary text-headline-md tracking-tighter mix-blend-difference">
            Zon Bumijaya
          </div>
          <nav className="hidden md:flex gap-12 items-center font-body uppercase text-[11px] tracking-[0.2em] font-semibold text-on-background">
            {['Who We Are', 'Products', 'Process'].map((item) => (
              <Link key={item} href={item === 'Process' ? '#how-we-work' : `#${item.toLowerCase().replace(/ /g, '-')}`} className="hover:text-primary transition-colors">
                {item}
              </Link>
            ))}
            <Link href="/shop" className="text-primary hover:text-on-background transition-colors font-bold flex items-center gap-1 border border-primary/30 px-3 py-1 rounded-full">
              STORE
            </Link>
            <ThemeToggle />
          </nav>
        </div>
      </header>

      <main className="flex-grow">
        {/* Artisan Hero Section */}
        <section className="relative h-screen flex items-center justify-center px-gutter overflow-hidden">
          <div className="absolute inset-0 z-0 bg-background overflow-hidden">
            <Image ref={heroImageRef} src="/hero_timber.png" alt="Industrial Timber" fill className="object-cover scale-110 origin-top opacity-60" priority unoptimized={true} />
            <div className="absolute inset-0 bg-gradient-to-b from-black/40 via-black/10 to-black/70"></div>
          </div>

          <div className="z-10 w-full max-w-container-max mx-auto flex flex-col justify-center h-full pt-20" style={{ perspective: 1000 }}>
            <div className="mb-4 overflow-hidden">
              <div className="hero-badge inline-block">
                <span className="font-body text-primary uppercase tracking-[0.3em] text-xs font-bold border-b border-primary/30 pb-2">Crafting since 1994</span>
              </div>
            </div>
            
            <h1 className="font-display text-[12vw] md:text-[8vw] text-white leading-[0.9] tracking-tighter w-[120%] -ml-[5px]">
              <div className="overflow-hidden p-1 -m-1"><div className="hero-text-line">The Master's</div></div>
              <div className="overflow-hidden p-1 -m-1"><div className="hero-text-line text-primary italic">Cut.</div></div>
            </h1>
            
            <div className="hero-button absolute bottom-16 right-gutter flex gap-6">
              <Link href="/shop">
                <MagneticButton className="bg-primary text-on-primary font-body uppercase text-[10px] tracking-widest px-8 py-5 rounded-full hover:bg-gold-muted flex items-center gap-2 shadow-[0_0_30px_rgba(233,193,118,0.3)]">
                  Enter Store
                </MagneticButton>
              </Link>
            </div>
          </div>
        </section>

        {/* The Living Wood - Asymmetrical About */}
        <section id="who-we-are" className="py-[150px] px-gutter bg-background relative overflow-hidden">
          <div className="absolute top-0 right-0 text-[30vw] font-display text-white/5 leading-none select-none z-0 tracking-tighter">1994</div>
          
          <div className="max-w-container-max mx-auto relative z-10">
            <div className="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
              <div className="md:col-span-5 relative">
                <div className="aspect-[3/4] relative border border-white/10 p-2 reveal-image bg-surface-dim">
                  <div className="relative w-full h-full overflow-hidden">
                    <Image src="/laminated_wood.png" alt="Wood details" fill className="object-cover scale-105" unoptimized={true} />
                  </div>
                </div>
                <div className="absolute -bottom-16 -right-16 aspect-square w-64 border border-primary/20 bg-surface-dim p-2 hidden md:block backdrop-blur-md reveal-image">
                  <div className="relative w-full h-full overflow-hidden">
                    <Image src="/finger_joint.png" alt="Joinery" fill className="object-cover scale-105" unoptimized={true} />
                  </div>
                </div>
              </div>
              
              <div className="md:col-span-6 md:col-start-7 pt-12 md:pt-0">
                <h2 className="font-display text-headline-xl text-on-background mb-8 reveal-up">
                  Raw nature meets <br /><span className="text-primary italic">industrial precision.</span>
                </h2>
                <p className="font-body text-on-surface-variant text-lg leading-relaxed mb-6 reveal-up">
                  For three decades, Zon Bumijaya has redefined timber manufacturing. We don't just cut wood; we engineer structural perfection using state-of-the-art CNC machinery to maximize yield and eliminate waste.
                </p>
                <p className="font-body text-on-surface-variant text-lg leading-relaxed reveal-up">
                  From heavy-duty solid wood pallets to architectural laminated beams, our products form the invisible backbone of global supply chains.
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* Bespoke Products Section */}
        <section id="products" className="py-[100px] px-gutter bg-surface-dim relative">
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary/5 via-transparent to-transparent"></div>
          
          <div className="max-w-container-max mx-auto relative z-10">
            <div className="flex justify-between items-end mb-16 border-b border-white/10 pb-8 reveal-up">
              <h2 className="font-display text-headline-xl text-on-background">The Collection</h2>
              <Link href="/shop" className="text-primary font-body uppercase text-xs tracking-widest hover:text-on-background transition-colors">View Live Directory</Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {products.map((product, idx) => (
                <Link key={product.id || idx} href={`/shop/${product.sku}`} className="group cursor-pointer block reveal-up">
                  <div className="aspect-[4/5] relative overflow-hidden mb-6 bg-background">
                    <Image src={product.img} alt={product.title} fill className="object-cover transition-transform duration-[1.5s] ease-out group-hover:scale-110" unoptimized={true} />
                    <div className="absolute inset-0 border border-white/0 group-hover:border-primary/50 transition-colors duration-500 m-4"></div>
                  </div>
                  <h3 className="font-display text-2xl text-on-background mb-2">{product.title}</h3>
                  <p className="font-body text-sm text-on-surface-variant uppercase tracking-widest line-clamp-2">{product.desc}</p>
                </Link>
              ))}
            </div>
          </div>
        </section>

        {/* Sticky Horizontal Timeline - True Conveyor Belt via ScrollTrigger */}
        <section id="how-we-work" ref={horizontalScrollRef} className="h-screen relative bg-background overflow-hidden">
          <div className="h-full flex flex-col justify-center px-gutter pt-20 pb-20">
             <div className="mb-12 shrink-0">
               <span className="font-body text-primary uppercase tracking-[0.3em] text-xs font-bold">The Process</span>
             </div>
             
             {/* The Track that moves left */}
             <div ref={horizontalWrapRef} className="flex gap-24 items-center h-full w-max">
                {[1, 2, 3, 4, 5, 6, 7].map((step) => {
                  const images = ['/hero_timber.png', '/solid_pallet.png', '/laminated_wood.png', '/finger_joint.png'];
                  const stepImage = images[(step - 1) % images.length];

                  return (
                    <div key={step} className="w-[85vw] md:w-[45vw] shrink-0 border-l border-on-background/10 pl-8 relative flex flex-col md:flex-row gap-8 items-center">
                      <div className="absolute -left-3 top-0 w-6 h-6 bg-background border border-primary flex items-center justify-center text-primary font-body text-[10px] rounded-full">
                        {step}
                      </div>
                      <div className="flex-1">
                        <h3 className="font-display text-5xl md:text-7xl text-on-background mb-6 mt-12">Phase {step}</h3>
                        <p className="font-body text-on-surface-variant text-xl md:text-2xl leading-relaxed max-w-lg">
                          Meticulous industrial grading and precision cutting tailored to client specifications. Every millimetre accounted for before proceeding down the line.
                        </p>
                      </div>
                      <div className="flex-1 w-full aspect-video relative overflow-hidden bg-surface-variant/30 hidden md:block">
                        <Image src={stepImage} alt={`Phase ${step}`} fill className="object-cover" unoptimized={true} />
                      </div>
                    </div>
                  );
                })}
             </div>
          </div>
        </section>

        {/* FAQ Accordion */}
        <section className="py-[150px] px-gutter bg-surface-dim border-t border-white/5 relative z-10">
          <div className="max-w-4xl mx-auto">
            <h2 className="font-display text-headline-xl text-on-background mb-16 text-center reveal-up">Inquiries</h2>
            <div className="space-y-2 reveal-up">
              {[
                { q: "What types of timber do you process?", a: "We specialize in processing various hardwoods and softwoods suitable for industrial applications, including our core products: solid wood pallets, laminated wood, and finger joints." },
                { q: "Do you offer custom sizing?", a: "Yes, our state-of-the-art CNC machinery allows us to cut and size timber to your exact operational specifications with minimal waste." },
                { q: "Where do you deliver?", a: "We manage industrial-scale shipping globally, with a strong focus on Malaysia and the broader Southeast Asian region." }
              ].map((faq, i) => (
                <div key={i} className="border-b border-white/10 overflow-hidden">
                  <button onClick={() => toggleFaq(i)} className="w-full py-8 flex justify-between items-center text-left group">
                    <h4 className={`font-display text-2xl transition-colors ${activeFaq === i ? 'text-primary' : 'text-on-background group-hover:text-primary'}`}>
                      {faq.q}
                    </h4>
                    <ChevronDown className={`text-primary transition-transform duration-500 ${activeFaq === i ? 'rotate-180' : ''}`} />
                  </button>
                  <div 
                    className="overflow-hidden transition-all duration-500 ease-in-out"
                    style={{ height: activeFaq === i ? 'auto' : '0px', opacity: activeFaq === i ? 1 : 0 }}
                  >
                    <p className="font-body text-on-surface-variant pb-8 text-lg max-w-2xl">
                      {faq.a}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="bg-background border-t border-white/5 py-24 px-gutter relative z-10">
        <div className="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-4 gap-16">
          <div className="col-span-1 md:col-span-2">
            <h2 className="font-display text-4xl text-primary mb-8 italic">Zon Bumijaya</h2>
            <p className="font-body text-on-surface-variant text-sm max-w-sm mb-12">
              A meticulous process ensuring industrial-scale precision and artisanal quality for every piece of timber.
            </p>
            <p className="font-body text-on-background/30 text-xs">
              © 2024 Zon Bumijaya Enterprise. SSM: 202003035274.
            </p>
          </div>
          <div>
            <h4 className="font-body uppercase text-[10px] text-on-background tracking-[0.2em] mb-8">Navigation</h4>
            <ul className="font-body text-on-surface-variant space-y-4 text-sm">
              <li><Link href="#who-we-are" className="hover:text-primary transition-colors">Who We Are</Link></li>
              <li><Link href="#products" className="hover:text-primary transition-colors">The Collection</Link></li>
              <li><Link href="#how-we-work" className="hover:text-primary transition-colors">The Process</Link></li>
            </ul>
          </div>
          <div>
             <h4 className="font-body uppercase text-[10px] text-on-background tracking-[0.2em] mb-8">Contact</h4>
             <ul className="font-body text-on-surface-variant space-y-4 text-sm">
               <li>saleszonbumi@gmail.com</li>
               <li>+60 11-3651 7252</li>
               <li className="leading-relaxed">
                 No. 95A, Jln Tanjong 1,<br />
                 Tmn Desa Cemerlang,<br />
                 81800 Ulu Tiram, Johor
               </li>
             </ul>
          </div>
        </div>
      </footer>

      {/* Floating WhatsApp Button */}
      <MagneticButton className="fixed bottom-8 right-8 bg-[#25D366] text-white p-4 rounded-full shadow-[0_0_20px_rgba(37,211,102,0.3)] z-50 flex items-center justify-center">
        <a href="https://wa.me/601136517252" target="_blank" rel="noopener noreferrer">
          <MessageCircle size={24} />
        </a>
      </MagneticButton>
    </div>
  );
}
