<?php wp_footer();
    $whatsapp  = get_theme_mod('hodcode_whatsapp');
    $telegram   = get_theme_mod('hodcode_telegram');
    $linkedin = get_theme_mod('hodcode_linkedin');
    ?>

 <footer class=" justify-center items-center border-t border-gray-300 mx-auto py-5 lg:py-20 flex flex-wrap gap-5">
     <div class="pr-2 w-13 h-13 flex items-center">
        <?php if (function_exists("the_custom_logo")) {
             the_custom_logo();
            } ?>
     </div>
     <div class="grow text-blue-500 text-center">
        <a href="http://zistak.hodecode.ir/privacy-policy">
            © کلیه حقوق این سایت برای زیستاک محفوظ می‌باشد
        </a>
     </div>
     <div class="flex flex-wrap gap-3 content-center">
         <?php
            $whatsapp = get_theme_mod('hodcode_whatsapp');
            if ($whatsapp):
                // ساخت لینک واتساپ با فرمت رسمی:
                $whatsapp_link = 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp); // فقط اعداد رو نگه می‌داره
            ?>
             <a href="<?php echo esc_url($whatsapp_link); ?>" target="_blank" class="aspect-square w-10 items-center flex rounded-full border-2 border-gray-300 justify-center">
                 <!-- آیکون واتساپ SVG -->
                 <svg width="14" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill="#0A142F" d="M20.52 3.48A11.95 11.95 0 0012 0C5.372 0 0 5.372 0 12a11.91 11.91 0 001.732 6.008L0 24l6.1-1.7A11.97 11.97 0 0012 24c6.628 0 12-5.372 12-12a11.95 11.95 0 00-3.48-8.52zm-8.14 16.66a7.9 7.9 0 01-4.03-1.14l-.29-.17-3.03.84.81-2.95-.19-.3a7.93 7.93 0 1111.31 0 7.88 7.88 0 01-4.6 2.72zm4.07-5.1c-.22-.11-1.3-.64-1.5-.71-.2-.07-.34-.11-.48.11-.14.22-.56.7-.68.85-.12.14-.23.16-.43.05-.2-.11-.85-.31-1.62-1-.6-.53-1-.93-1.12-1.13-.12-.22-.01-.34.09-.45.09-.09.2-.22.3-.33.1-.11.13-.18.2-.3.06-.11.03-.22-.01-.33-.04-.11-.48-1.16-.66-1.6-.17-.42-.34-.36-.48-.37-.12 0-.26 0-.4 0-.14 0-.33.05-.5.23-.17.17-.66.64-.66 1.56s.68 1.81.77 1.94c.09.14 1.33 2.04 3.22 2.85.45.19.8.3 1.07.38.45.13.86.11 1.19.07.36-.05 1.3-.53 1.48-1.04.18-.5.18-.93.13-1.04-.05-.1-.18-.16-.4-.27z" />
                 </svg>
             </a>
         <?php endif; ?>

         <?php if ($telegram): ?>
             <a href="<?php echo esc_url($telegram); ?>" target="_blank" class="aspect-square w-12 items-center flex rounded-full border-2 border-gray-300 justify-center">
                 <svg width="20" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M21.5 3.5L2.5 10.5L8 13L20 6L15 17L13 14L10 15L21.5 3.5Z" fill="#000000" />
                 </svg>
             </a>
         <?php endif; ?>
            
         <?php if ($linkedin): ?>
             <a href="<?php echo esc_url($linkedin); ?>" target="_blank" class="aspect-square w-12 items-center flex rounded-full border-2 border-gray-300 justify-center">
                 <svg width="20" height="22" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M3 14.5H0V5.5H3V14.5Z" fill="#000000" />
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M1.49108 3.5H1.47404C0.578773 3.5 0 2.83303 0 1.99948C0 1.14831 0.5964 0.5 1.50865 0.5C2.42091 0.5 2.98269 1.14831 3 1.99948C3 2.83303 2.42091 3.5 1.49108 3.5Z" fill="#000000" />
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9999 14.4998H11.0519V9.79535C11.0519 8.61371 10.6253 7.80738 9.55814 7.80738C8.74368 7.80738 8.25855 8.35096 8.04549 8.87598C7.96754 9.06414 7.94841 9.3263 7.94841 9.58911V14.5H5C5 14.5 5.03886 6.53183 5 5.70672H7.94841V6.95221C8.33968 6.35348 9.04046 5.5 10.6057 5.5C12.5456 5.5 14 6.75705 14 9.45797L13.9999 14.4998Z" fill="#000000" />
                 </svg>
             </a>
         <?php endif; ?>

     </div>

     <div class="footer-links">
             <a href="http://zistak.hodecode.ir/درباره ما"
                 class="about-us-link"> درباره ما </a>
         </div>
         <!DOCTYPE html>
         <html lang="fa">

         <head>
             <meta charset="UTF-8">
             <title>hodcode پشتیبانی</title>
             <link rel="stylesheet" href="style.css">
         </head>
 </footer>
 <script id="chatway" async="true" src="https://cdn.chatway.app/widget.js?id=cwB0PsyycYVm"></script>
 </body>
 </html>