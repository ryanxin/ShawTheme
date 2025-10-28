// Shaw Global Theme - Header Scroll Effects
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.transparent-header');
    const body = document.body;
    
    if (!header) return;
    
    // 添加滚动监听器
    let lastScrollTop = 0;
    let ticking = false;
    
    function updateHeader() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // 添加滚动类
        if (scrollTop > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        // 检测滚动方向（可选：隐藏/显示头部）
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // 向下滚动 - 可以隐藏头部
            // header.style.transform = 'translateY(-100%)';
        } else {
            // 向上滚动 - 显示头部
            header.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateHeader);
            ticking = true;
        }
    }
    
    window.addEventListener('scroll', requestTick);
    
    // 检测页面类型
    if (body.classList.contains('home')) {
        // 首页特殊处理
        header.style.position = 'absolute';
        header.style.top = '0';
    } else {
        // 其他页面使用固定定位
        header.style.position = 'fixed';
    }
    
    // 处理移动端菜单
    const mobileMenuToggle = document.querySelector('.wp-block-navigation__responsive-container-open');
    const mobileMenu = document.querySelector('.wp-block-navigation__responsive-container');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            body.classList.toggle('mobile-menu-open');
        });
        
        // 点击外部关闭菜单
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                body.classList.remove('mobile-menu-open');
            }
        });
    }
});

// 平滑滚动到锚点
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href^="#"]');
    if (link) {
        e.preventDefault();
        const targetId = link.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            const headerHeight = document.querySelector('.transparent-header')?.offsetHeight || 0;
            const targetPosition = targetElement.offsetTop - headerHeight - 20;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    }
});

// 回到顶部功能
document.addEventListener('click', function(e) {
    const scrollToTopBtn = e.target.closest('.scroll-to-top .wp-block-button__link');
    if (scrollToTopBtn) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
});

// 显示/隐藏回到顶部按钮
function toggleScrollToTop() {
    const scrollToTopBtn = document.querySelector('.scroll-to-top');
    if (!scrollToTopBtn) return;
    
    if (window.pageYOffset > 300) {
        scrollToTopBtn.style.opacity = '1';
        scrollToTopBtn.style.visibility = 'visible';
    } else {
        scrollToTopBtn.style.opacity = '0';
        scrollToTopBtn.style.visibility = 'hidden';
    }
}

// 监听滚动事件
window.addEventListener('scroll', function() {
    requestAnimationFrame(toggleScrollToTop);
});

// 初始化回到顶部按钮状态
document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.querySelector('.scroll-to-top');
    if (scrollToTopBtn) {
        scrollToTopBtn.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';
        scrollToTopBtn.style.opacity = '0';
        scrollToTopBtn.style.visibility = 'hidden';
    }
});