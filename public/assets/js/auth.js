'use strict'; // Modo estricto: convierte errores silenciosos en errores visibles

// Se espera a que el DOM esté listo antes de buscar elementos
document.addEventListener('DOMContentLoaded', () => {
  const formulario = document.getElementById('formAuth');
  if (!formulario) return; // Si esta página no tiene formulario, no hacemos nada

  // ---------- Botón "ojo": alterna entre ocultar y mostrar la contraseña ----------
  formulario.querySelectorAll('.ojo').forEach((ojo) => {
    ojo.addEventListener('click', () => {
      // El input de contraseña está dentro del mismo .campo que el botón del ojo
      const input = ojo.closest('.campo').querySelector('input');
      const mostrar = input.type === 'password';
      input.type = mostrar ? 'text' : 'password';          // Cambia el tipo del input
      ojo.setAttribute('aria-pressed', String(mostrar));   // El CSS usa este atributo para tachar el ojo
      ojo.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
  });

  const zonaMensaje = document.getElementById('mensaje');
  const boton = formulario.querySelector('button[type="submit"]');
  const esRegistro = formulario.dataset.formulario === 'registro';

  /** Muestra un mensaje con estilo de error u ok */
  const mostrarMensaje = (texto, tipo) => {
    zonaMensaje.textContent = texto;
    zonaMensaje.className = `mensaje ${tipo}`;
  };

  /**
   * Verifica campos vacíos (incluye los que solo tienen espacios)
   * Complementa a "required" del HTML. Devuelve true si todo está completo
   */
  const camposCompletos = () => {
    const campos = formulario.querySelectorAll('input:not([type="hidden"])');
    for (const campo of campos) {
      if (campo.value.trim() === '') {
        campo.focus();                          // Lleva el cursor al primer campo vacío
        mostrarMensaje('Completá todos los campos.', 'error');
        return false;
      }
    }
    return true;
  };

  /** Verifica que password y confirmación coincidan (solo en el alta) */
  const passwordsCoinciden = () => {
    const pass = formulario.elements['password'].value;
    const conf = formulario.elements['confirmar'].value;
    if (pass !== conf) {
      formulario.elements['confirmar'].focus();
      mostrarMensaje('Las contraseñas no coinciden.', 'error');
      return false;
    }
    return true;
  };

  // Aviso en tiempo real al escribir la confirmación (mejor experiencia de usuario)
  if (esRegistro) {
    const confirmar = formulario.elements['confirmar'];
    confirmar.addEventListener('input', () => {
      const coincide = confirmar.value === formulario.elements['password'].value;
      // setCustomValidity integra el error con la validación nativa del navegador
      confirmar.setCustomValidity(coincide ? '' : 'Las contraseñas no coinciden.');
    });
  }

  // Manejo del envío del formulario
  formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault(); // Cancela el envío tradicional (que recargaría la página)
    mostrarMensaje('', '');

    // Validaciones en cliente
    if (!camposCompletos()) return;
    if (esRegistro && !passwordsCoinciden()) return;

    // Se arma el objeto a enviar con los valores del formulario
    const datos = Object.fromEntries(new FormData(formulario).entries());
    datos.csrf = document.getElementById('csrf').value;

    boton.disabled = true; // Evita doble envío mientras se espera la respuesta

    try {
      // fetch con async/await: POST con cuerpo JSON hacia el endpoint de PHP
      const respuesta = await fetch(formulario.dataset.endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos),
        credentials: 'same-origin', // Envía la cookie de sesión
      });

      const resultado = await respuesta.json(); // El servidor siempre responde JSON

      if (resultado.ok) {
        mostrarMensaje(resultado.mensaje, 'ok');
        // Pequeña pausa para que se lea el mensaje y luego se redirige
        setTimeout(() => { window.location.href = resultado.redirect; }, 900);
      } else {
        mostrarMensaje(resultado.mensaje, 'error');
        boton.disabled = false;
      }
    } catch (error) {
      // Falla de red o respuesta no-JSON (ej.: Apache caído)
      mostrarMensaje('No se pudo contactar al servidor. Intentá de nuevo.', 'error');
      boton.disabled = false;
    }
  });
});
