// Animações com GSAP para o sistema de reserva de auditório
const gsap = window.gsap

document.addEventListener("DOMContentLoaded", () => {
  // Animação da página de login
  if (document.querySelector(".login-box")) {
    gsap.from(".login-box", {
      duration: 0.6,
      y: 30,
      opacity: 0,
      ease: "power2.out",
    })
  }

  if (document.querySelector(".dashboard")) {
    // Ensure all elements are visible by default
    const cards = document.querySelectorAll(".card, .stat-card, .charts-section, .reservas-table")
    cards.forEach((el) => {
      el.style.opacity = "1"
      el.style.visibility = "visible"
    })

    // Simple fade in for header
    gsap.from(".dashboard-header", {
      duration: 0.4,
      y: -20,
      opacity: 0,
      ease: "power2.out",
    })

    // Simple fade in for stat cards
    gsap.from(".stat-card", {
      duration: 0.5,
      y: 20,
      opacity: 0,
      stagger: 0.1,
      ease: "power2.out",
    })

    // Simple fade in for charts
    gsap.from(".charts-section", {
      duration: 0.5,
      opacity: 0,
      delay: 0.3,
      ease: "power2.out",
    })

    // Simple fade in for table
    gsap.from(".reservas-table", {
      duration: 0.5,
      opacity: 0,
      delay: 0.4,
      ease: "power2.out",
    })
  }

  const buttons = document.querySelectorAll(".btn")
  buttons.forEach((button) => {
    button.addEventListener("mouseenter", () => {
      gsap.to(button, {
        duration: 0.2,
        scale: 1.03,
        ease: "power1.out",
      })
    })

    button.addEventListener("mouseleave", () => {
      gsap.to(button, {
        duration: 0.2,
        scale: 1,
        ease: "power1.out",
      })
    })
  })
})
