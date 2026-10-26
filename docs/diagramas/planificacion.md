# Planificación del trabajo

Informe, punto 8. Las fechas de fin de las tareas terminadas salen del historial de commits
del repositorio y de las versiones del informe (22/08, 07/09 y 05/10); las de inicio son
estimadas. Las tareas del cierre están planificadas.

```mermaid
%%{init: {'theme': 'neutral'}}%%
gantt
    title AeroEB · planificación y avance (2026)
    dateFormat YYYY-MM-DD
    axisFormat %d/%m
    section Análisis y diseño
    Objetivos, audiencia (puntos 1 a 3)         :done, a1, 2026-08-10, 2026-08-22
    Corrección de la docente (v1.1)              :done, a2, 2026-08-25, 2026-09-07
    Contenidos, estructura y navegación (4 a 6)  :done, a3, 2026-09-08, 2026-10-05
    section Construcción
    Repositorio y base de datos                  :done, b1, 2026-08-26, 2026-08-27
    Núcleo MVC y front controller                :done, b2, 2026-08-27, 2026-08-29
    Sitio público                                :done, b3, 2026-08-31, 2026-09-18
    Cuentas de usuario                           :done, b4, 2026-10-06, 2026-10-20
    Motor de reservas y pago                     :done, b5, 2026-10-06, 2026-10-20
    Panel de la aerolínea                        :done, b6, 2026-10-13, 2026-10-20
    Panel de administración                      :done, b7, 2026-10-14, 2026-10-21
    Diseño visual y accesibilidad                :done, b8, 2026-10-19, 2026-10-22
    section Verificación y puesta en marcha
    Revisión contra enunciado y wireframes       :done, c1, 2026-10-23, 2026-10-25
    Despliegue en alwaysdata                     :done, c2, 2026-10-25, 1d
    Validaciones W3C, Lighthouse y TAW           :active, c3, 2026-10-25, 2026-10-28
    section Cierre
    Informe final                                :d1, 2026-10-26, 2026-11-06
    Presentación oral                            :d2, 2026-11-02, 2026-11-09
```

Imagen: [`png/gantt.png`](png/gantt.png)
