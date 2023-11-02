document.querySelectorAll(".hover-container").forEach((container) => {
    // reseting styles for the element when the mouse exits the element
    container.onmouseleave = (e) => {
        const overlayChild = e.target.querySelector(".overlay");

        e.target.style.transform = "rotateY(0) rotateX(0)";
        overlayChild.style.background = "transparent";
    };

    // adding a listener to style the element when the mouse moves inside the element
    container.addEventListener("mousemove", (e) => {
        const rect = e.target.getBoundingClientRect();
        const x = e.clientX - rect.left; //x position within the element.
        const y = e.clientY - rect.top; //y position within the element.
        const width = e.target.offsetWidth;
        const height = e.target.offsetHeight;

        const overlayChild = e.target.querySelector(".overlay");

        // the values can be tweaked as per personal requirements
        e.target.style.transform = `rotateY(${-(0.5 - x / width) * 30
            }deg) rotateX(${(y / height - 0.5) * 30}deg)`;

        overlayChild.style.background = `radial-gradient(
            circle at ${x}px ${y}px,
            rgba(255, 255, 255, 0.2),
            rgba(0, 0, 0, 0.2)
        )`;
    });
})