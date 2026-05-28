const currentPage =
window.location.pathname.split("/").pop();

const menuItems =
document.querySelectorAll(".menu-item");

menuItems.forEach(item => {

    const href = item.getAttribute("href");

    if(href === currentPage){

        item.classList.add(
            "bg-blue-600",
            "text-white"
        );

    }

});