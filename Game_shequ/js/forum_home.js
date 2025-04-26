document.addEventListener("DOMContentLoaded", function () {
    const items = document.querySelectorAll(".carousel-item");
    const dots = document.querySelectorAll(".carousel-dots .dot");
    let currentIndex = 0; // 当前显示的轮播图索引
    let interval; // 定时器变量
    let isLoggedIn = false; // 登录状态标志

    // 显示指定索引的轮播图
    function showSlide(index) {
        if (index < 0) index = items.length - 1;
        if (index >= items.length) index = 0;

        items.forEach((item, i) => {
            item.classList.toggle("active", i === index);
            dots[i].classList.toggle("active", i === index);
        });

        currentIndex = index;
    }

    // 自动轮播功能
    function startAutoSlide() {
        interval = setInterval(() => showSlide(currentIndex + 1), 3000);
    }

    // 停止自动轮播功能
    function stopAutoSlide() {
        clearInterval(interval);
    }

    // 点击导航点切换轮播图
    dots.forEach((dot, i) => {
        dot.addEventListener("click", () => {
            stopAutoSlide();
            showSlide(i);
            startAutoSlide();
        });
    });

    // 初始化轮播图
    showSlide(currentIndex);
    startAutoSlide();

});
