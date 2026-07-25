<?php
$enc = $this->encoder();
?>
<div class="section aimeos catalog-list py-16" style="background-color: white !important;">
    <div class="max-w-[1280px] mx-auto px-8">
        
        <div class="mb-12 border-b border-gray-200 pb-8 reveal-up">
            <h1 class="text-[#1e3a8a] text-5xl md:text-6xl tracking-tight font-bold" style="font-family: var(--font-playfair), serif;">Zon Bumijaya Directory</h1>
            <p class="text-gray-500 mt-4 max-w-2xl text-sm tracking-widest uppercase font-semibold">Industrial Timber Solutions & Engineered Products</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach( $this->get( 'listProductItems', map() ) as $id => $productItem ) : ?>
                <?php 
                    $mediaItems = $productItem->getRefItems( 'media', 'default', 'default' );
                    $priceItems = $productItem->getRefItems( 'price', 'default', 'default' );
                    $textItems = $productItem->getRefItems( 'text', 'short', 'default' );
                    
                    $imageUrl = $mediaItems->isEmpty() ? '' : $this->content( $mediaItems->first()->getPreview( true ), $mediaItems->first()->getFileSystem() );
                    $price = $priceItems->isEmpty() ? '0.00' : $priceItems->first()->getValue();
                    $desc = $textItems->isEmpty() ? '' : $textItems->first()->getContent();
                ?>
                <a href="<?= $enc->attr( $this->link( 'client/html/catalog/detail/url', ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId(), 'd_pos' => $id] ) ) ?>" class="group relative bg-white border border-gray-200 shadow-sm hover:shadow-xl rounded-2xl overflow-hidden hover:border-[#f59e0b] transition-all duration-300">
                    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-50 flex items-center justify-center p-4">
                        <?php if($imageUrl): ?>
                            <img src="<?= $enc->attr( $imageUrl ) ?>" alt="<?= $enc->attr( $productItem->getName() ) ?>" class="max-w-full max-h-full object-contain transition-transform duration-700 group-hover:scale-105">
                        <?php else: ?>
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3 gap-2">
                            <h3 class="text-[#1e293b] font-bold text-xl group-hover:text-[#1e3a8a] transition-colors leading-tight" style="font-family: var(--font-playfair), serif;"><?= $enc->html( $productItem->getName() ) ?></h3>
                            <span class="text-[#f59e0b] font-bold text-sm bg-amber-50 px-3 py-1 rounded-full whitespace-nowrap">$<?= $enc->html( $price ) ?></span>
                        </div>
                        <p class="text-gray-500 text-sm line-clamp-2 leading-relaxed"><?= $enc->html( $desc, $enc::TRUST ) ?></p>
                        
                        <div class="mt-6 pt-5 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-gray-400 text-[11px] font-bold uppercase tracking-widest group-hover:text-[#1e3a8a] transition-colors">View Details</span>
                            <form method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>" class="z-10 relative">
                                <?= $this->csrf()->formfield() ?>
                                <input type="hidden" name="b_action" value="add">
                                <input type="hidden" name="b_prod[0][prodid]" value="<?= $enc->attr( $productItem->getId() ) ?>">
                                <input type="hidden" name="b_prod[0][quantity]" value="1">
                                <button type="submit" class="bg-[#1e3a8a] text-white p-3 rounded-full hover:bg-[#f59e0b] hover:shadow-md transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
