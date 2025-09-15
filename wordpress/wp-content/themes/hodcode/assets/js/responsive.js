document.addEventListener("DOMContentLoaded", function () {
    // همه container ها رو بگیر
    const grids = document.querySelectorAll(".grid");
    grids.forEach(grid => {
      if (grid.classList.contains("grid-cols-4")) {
        grid.classList.add("md:grid-cols-2", "sm:grid-cols-1");
      }
      if (grid.classList.contains("grid-cols-3")) {
        grid.classList.add("sm:grid-cols-1");
      }
    });
  
    const flexes = document.querySelectorAll(".flex");
    flexes.forEach(flex => {
      if (flex.classList.contains("flex-row")) {
        flex.classList.add("sm:flex-col");
      }
    });
  });
  