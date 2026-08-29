<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
    <div data-theme="child" class="min-h-screen bg-theme relative overflow-hidden">

        <!-- Cartes décoratives -->
        <div class="absolute inset-0 pointer-events-none select-none opacity-70">

            <!-- 1 — voiture, coin haut gauche -->
            <div class="card-anim absolute w-40 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-rose-100"
                 style="top: 3%; left: 2%; --r: -10deg; --dy: -8px; --dur: 4s; --delay: 0s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-rose-400">
                    <rect x="8" y="35" width="84" height="28" rx="8" fill="currentColor"/>
                    <rect x="18" y="18" width="50" height="22" rx="8" fill="currentColor" opacity="0.7"/>
                    <rect x="22" y="22" width="18" height="13" rx="4" fill="white" opacity="0.5"/>
                    <rect x="44" y="22" width="18" height="13" rx="4" fill="white" opacity="0.5"/>
                    <circle cx="26" cy="63" r="10" fill="currentColor" opacity="0.4"/>
                    <circle cx="26" cy="63" r="5" fill="white" opacity="0.5"/>
                    <circle cx="74" cy="63" r="10" fill="currentColor" opacity="0.4"/>
                    <circle cx="74" cy="63" r="5" fill="white" opacity="0.5"/>
                    <circle cx="90" cy="46" r="5" fill="white" opacity="0.6"/>
                </svg>
            </div>

            <!-- 2 — ballon, haut gauche -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-amber-100"
                 style="top: 8%; left: 18%; --r: 8deg; --dy: -10px; --dur: 5.5s; --delay: 0.8s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-amber-400">
                    <ellipse cx="50" cy="30" rx="25" ry="27" fill="currentColor"/>
                    <ellipse cx="40" cy="18" rx="8" ry="10" fill="white" opacity="0.25"/>
                    <ellipse cx="50" cy="57" rx="4" ry="3" fill="currentColor" opacity="0.7"/>
                    <path d="M 50 60 Q 45 65 50 70" stroke="currentColor" stroke-width="2" fill="none" opacity="0.5"/>
                </svg>
            </div>

            <!-- 3 — étoile, haut centre-gauche -->
            <div class="card-anim absolute w-32 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-indigo-100"
                 style="top: 2%; left: 37%; --r: -5deg; --dy: -7px; --dur: 3.8s; --delay: 1.5s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-2 text-indigo-400">
                    <path d="M 50 8 L 58 30 L 82 30 L 63 45 L 70 68 L 50 54 L 30 68 L 37 45 L 18 30 L 42 30 Z" fill="currentColor"/>
                </svg>
            </div>

            <!-- 4 — soleil, haut centre-droite -->
            <div class="card-anim absolute w-32 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-yellow-100"
                 style="top: 5%; right: 22%; --r: 6deg; --dy: -6px; --dur: 4.5s; --delay: 2s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-yellow-400">
                    <line x1="50" y1="5" x2="50" y2="16" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="50" y1="59" x2="50" y2="70" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="6" y1="37" x2="17" y2="37" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="83" y1="37" x2="94" y2="37" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="16" y1="11" x2="24" y2="19" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="76" y1="55" x2="84" y2="63" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="84" y1="11" x2="76" y2="19" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <line x1="24" y1="55" x2="16" y2="63" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                    <circle cx="50" cy="37" r="16" fill="currentColor"/>
                </svg>
            </div>

            <!-- 5 — fusée, coin haut droite -->
            <div class="card-anim absolute w-40 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-teal-100"
                 style="top: -3%; right: -2%; --r: 10deg; --dy: -9px; --dur: 5s; --delay: 0.3s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-teal-400">
                    <path d="M 32 30 Q 50 5 68 30 Z" fill="currentColor" opacity="0.8"/>
                    <ellipse cx="50" cy="44" rx="18" ry="22" fill="currentColor"/>
                    <path d="M 34 58 L 20 72 L 34 66 Z" fill="currentColor" opacity="0.7"/>
                    <path d="M 66 58 L 80 72 L 66 66 Z" fill="currentColor" opacity="0.7"/>
                    <circle cx="50" cy="42" r="9" fill="white" opacity="0.4"/>
                    <ellipse cx="50" cy="70" rx="8" ry="5" fill="#fb923c" opacity="0.7"/>
                </svg>
            </div>

            <!-- 6 — maison, milieu gauche -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-pink-100"
                 style="top: 28%; left: 5%; --r: 5deg; --dy: -8px; --dur: 4.2s; --delay: 1.2s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-pink-400">
                    <rect x="18" y="38" width="64" height="32" rx="4" fill="currentColor"/>
                    <path d="M 12 40 L 50 8 L 88 40 Z" fill="currentColor" opacity="0.8"/>
                    <rect x="40" y="52" width="20" height="18" rx="4" fill="white" opacity="0.4"/>
                    <rect x="22" y="44" width="14" height="12" rx="3" fill="white" opacity="0.4"/>
                    <rect x="64" y="44" width="14" height="12" rx="3" fill="white" opacity="0.4"/>
                    <rect x="68" y="12" width="10" height="20" rx="2" fill="currentColor" opacity="0.7"/>
                </svg>
            </div>

            <!-- 7 — chat, centre gauche (derrière titre) -->
            <div class="card-anim absolute w-32 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-sky-100"
                 style="top: 40%; left: 30%; --r: 4deg; --dy: -6px; --dur: 3.5s; --delay: 2.5s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-sky-400">
                    <ellipse cx="50" cy="56" rx="22" ry="15" fill="currentColor"/>
                    <circle cx="50" cy="32" r="18" fill="currentColor"/>
                    <path d="M 35 20 L 30 8 L 42 16 Z" fill="currentColor"/>
                    <path d="M 65 20 L 70 8 L 58 16 Z" fill="currentColor"/>
                    <ellipse cx="43" cy="30" rx="5" ry="5" fill="white" opacity="0.7"/>
                    <ellipse cx="57" cy="30" rx="5" ry="5" fill="white" opacity="0.7"/>
                    <ellipse cx="43" cy="31" rx="2" ry="4" fill="currentColor" opacity="0.5"/>
                    <ellipse cx="57" cy="31" rx="2" ry="4" fill="currentColor" opacity="0.5"/>
                    <path d="M 47 37 L 50 40 L 53 37 Z" fill="white" opacity="0.5"/>
                    <line x1="28" y1="36" x2="44" y2="38" stroke="white" stroke-width="1.5" opacity="0.5"/>
                    <line x1="72" y1="36" x2="56" y2="38" stroke="white" stroke-width="1.5" opacity="0.5"/>
                    <path d="M 70 62 Q 85 55 83 70" stroke="currentColor" stroke-width="6" fill="none" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- 8 — poisson, centre droite (derrière titre) -->
            <div class="card-anim absolute w-32 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-emerald-100"
                 style="top: 44%; right: 28%; --r: -6deg; --dy: -7px; --dur: 4.8s; --delay: 1.8s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-emerald-400">
                    <ellipse cx="48" cy="37" rx="28" ry="18" fill="currentColor"/>
                    <path d="M 75 37 L 92 22 L 92 52 Z" fill="currentColor" opacity="0.8"/>
                    <circle cx="30" cy="33" r="6" fill="white" opacity="0.6"/>
                    <circle cx="29" cy="33" r="3" fill="currentColor" opacity="0.4"/>
                    <path d="M 20 40 Q 24 44 20 48" stroke="white" stroke-width="2" fill="none" opacity="0.5"/>
                    <circle cx="50" cy="30" r="5" stroke="white" stroke-width="1.5" fill="none" opacity="0.3"/>
                    <path d="M 42 19 Q 50 10 58 19" fill="currentColor" opacity="0.6"/>
                </svg>
            </div>

            <!-- 9 — casserole, milieu droite -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-orange-100"
                 style="top: 22%; right: 4%; --r: -8deg; --dy: -9px; --dur: 4s; --delay: 0.7s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-orange-400">
                    <rect x="20" y="28" width="55" height="38" rx="8" fill="currentColor"/>
                    <rect x="18" y="22" width="59" height="10" rx="5" fill="currentColor" opacity="0.8"/>
                    <rect x="42" y="14" width="11" height="10" rx="5" fill="currentColor" opacity="0.6"/>
                    <rect x="75" y="32" width="20" height="8" rx="4" fill="currentColor" opacity="0.7"/>
                    <circle cx="38" cy="10" r="4" fill="currentColor" opacity="0.3"/>
                    <circle cx="50" cy="6" r="5" fill="currentColor" opacity="0.25"/>
                    <circle cx="62" cy="9" r="3" fill="currentColor" opacity="0.3"/>
                </svg>
            </div>

            <!-- 10 — étoile, milieu droite bas -->
            <div class="card-anim absolute w-32 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-lime-100"
                 style="top: 58%; right: 7%; --r: 9deg; --dy: -7px; --dur: 5.2s; --delay: 1.5s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-2 text-lime-500">
                    <path d="M 50 8 L 58 30 L 82 30 L 63 45 L 70 68 L 50 54 L 30 68 L 37 45 L 18 30 L 42 30 Z" fill="currentColor"/>
                </svg>
            </div>

            <!-- 11 — feu d'artifice, milieu gauche bas -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-violet-100"
                 style="top: 58%; left: 10%; --r: -7deg; --dy: -10px; --dur: 5s; --delay: 0.5s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-2 text-violet-400">
                    <!-- burst gauche -->
                    <line x1="22" y1="48" x2="22" y2="36" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="14" y2="40" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="10" y2="48" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="14" y2="56" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="22" y2="60" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="30" y2="56" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="34" y2="48" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <line x1="22" y1="48" x2="30" y2="40" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                    <circle cx="22" cy="48" r="3" fill="currentColor"/>
                    <!-- burst centre -->
                    <line x1="52" y1="30" x2="52" y2="14" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="40" y2="20" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="36" y2="30" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="40" y2="42" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="52" y2="46" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="64" y2="42" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="68" y2="30" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <line x1="52" y1="30" x2="64" y2="20" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="52" cy="30" r="4" fill="#a78bfa"/>
                    <!-- burst droite -->
                    <line x1="78" y1="52" x2="78" y2="42" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="71" y2="45" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="68" y2="52" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="71" y2="59" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="78" y2="62" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="85" y2="59" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="88" y2="52" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <line x1="78" y1="52" x2="85" y2="45" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    <circle cx="78" cy="52" r="2.5" fill="currentColor" opacity="0.7"/>
                </svg>
            </div>

            <!-- 12 — dinosaure, coin bas gauche -->
            <div class="card-anim absolute w-40 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-sky-100"
                 style="bottom: 4%; left: 6%; --r: -10deg; --dy: -8px; --dur: 4.5s; --delay: 0.4s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-sky-400">
                    <ellipse cx="52" cy="50" rx="22" ry="16" fill="currentColor"/>
                    <ellipse cx="78" cy="32" rx="14" ry="10" fill="currentColor"/>
                    <ellipse cx="88" cy="36" rx="8" ry="6" fill="currentColor" opacity="0.8"/>
                    <circle cx="80" cy="28" r="4" fill="white" opacity="0.7"/>
                    <circle cx="81" cy="28" r="2" fill="currentColor" opacity="0.5"/>
                    <path d="M 68 48 L 72 58 L 76 55" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M 30 52 Q 18 55 12 65" stroke="currentColor" stroke-width="8" fill="none" stroke-linecap="round"/>
                    <rect x="44" y="64" width="8" height="10" rx="4" fill="currentColor"/>
                    <rect x="58" y="64" width="8" height="10" rx="4" fill="currentColor"/>
                    <path d="M 38 40 L 42 32 L 46 40 L 50 33 L 54 40 L 58 32 L 62 40" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- 13 — casserole, bas centre-gauche -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-orange-100"
                 style="bottom: 8%; left: 28%; --r: 7deg; --dy: -9px; --dur: 3.8s; --delay: 1.8s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-orange-400">
                    <rect x="20" y="28" width="55" height="38" rx="8" fill="currentColor"/>
                    <rect x="18" y="22" width="59" height="10" rx="5" fill="currentColor" opacity="0.8"/>
                    <rect x="42" y="14" width="11" height="10" rx="5" fill="currentColor" opacity="0.6"/>
                    <rect x="75" y="32" width="20" height="8" rx="4" fill="currentColor" opacity="0.7"/>
                </svg>
            </div>

            <!-- 14 — voiture, bas centre-droite -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-cyan-100"
                 style="bottom: 6%; right: 22%; --r: -8deg; --dy: -8px; --dur: 4.3s; --delay: 0.9s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-cyan-400">
                    <rect x="8" y="35" width="84" height="28" rx="8" fill="currentColor"/>
                    <rect x="18" y="18" width="50" height="22" rx="8" fill="currentColor" opacity="0.7"/>
                    <rect x="22" y="22" width="18" height="13" rx="4" fill="white" opacity="0.5"/>
                    <rect x="44" y="22" width="18" height="13" rx="4" fill="white" opacity="0.5"/>
                    <circle cx="26" cy="63" r="10" fill="currentColor" opacity="0.4"/>
                    <circle cx="26" cy="63" r="5" fill="white" opacity="0.5"/>
                    <circle cx="74" cy="63" r="10" fill="currentColor" opacity="0.4"/>
                    <circle cx="74" cy="63" r="5" fill="white" opacity="0.5"/>
                </svg>
            </div>

            <!-- 16 — lune, centre haut (derrière titre) -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-fuchsia-100"
                 style="top: 18%; left: 50%; --r: -4deg; --dy: -8px; --dur: 4.6s; --delay: 2.2s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-fuchsia-400">
                    <path d="M 55 10 A 28 28 0 1 0 55 65 A 20 20 0 1 1 55 10 Z" fill="currentColor"/>
                    <circle cx="68" cy="18" r="3" fill="currentColor" opacity="0.4"/>
                    <circle cx="76" cy="30" r="2" fill="currentColor" opacity="0.3"/>
                    <circle cx="72" cy="46" r="2.5" fill="currentColor" opacity="0.35"/>
                </svg>
            </div>

            <!-- 17 — piano, centre bas (derrière titre) -->
            <div class="card-anim absolute w-36 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-green-100"
                 style="top: 62%; left: 44%; --r: 5deg; --dy: -7px; --dur: 5.3s; --delay: 1.1s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-green-600">
                    <!-- caisse du piano -->
                    <rect x="8" y="20" width="84" height="48" rx="5" fill="currentColor"/>
                    <!-- touches blanches -->
                    <rect x="12" y="30" width="10" height="30" rx="2" fill="white" opacity="0.9"/>
                    <rect x="24" y="30" width="10" height="30" rx="2" fill="white" opacity="0.9"/>
                    <rect x="36" y="30" width="10" height="30" rx="2" fill="white" opacity="0.9"/>
                    <rect x="48" y="30" width="10" height="30" rx="2" fill="white" opacity="0.9"/>
                    <rect x="60" y="30" width="10" height="30" rx="2" fill="white" opacity="0.9"/>
                    <rect x="72" y="30" width="10" height="30" rx="2" fill="white" opacity="0.9"/>
                    <!-- touches noires -->
                    <rect x="19" y="30" width="7" height="18" rx="2" fill="currentColor" opacity="0.7"/>
                    <rect x="31" y="30" width="7" height="18" rx="2" fill="currentColor" opacity="0.7"/>
                    <rect x="55" y="30" width="7" height="18" rx="2" fill="currentColor" opacity="0.7"/>
                    <rect x="67" y="30" width="7" height="18" rx="2" fill="currentColor" opacity="0.7"/>
                    <!-- couvercle -->
                    <rect x="8" y="20" width="84" height="12" rx="5" fill="currentColor" opacity="0.5"/>
                </svg>
            </div>

            <!-- 15 — ballon, coin bas droite -->
            <div class="card-anim absolute w-40 aspect-[4/3] rounded-2xl shadow-sm overflow-hidden bg-purple-100"
                 style="bottom: -3%; right: -1%; --r: -12deg; --dy: -10px; --dur: 5s; --delay: 1.2s">
                <svg viewBox="0 0 100 75" class="w-full h-full p-3 text-purple-400">
                    <ellipse cx="50" cy="30" rx="25" ry="27" fill="currentColor"/>
                    <ellipse cx="40" cy="18" rx="8" ry="10" fill="white" opacity="0.25"/>
                    <ellipse cx="50" cy="57" rx="4" ry="3" fill="currentColor" opacity="0.7"/>
                    <path d="M 50 60 Q 45 65 50 70" stroke="currentColor" stroke-width="2" fill="none" opacity="0.5"/>
                </svg>
            </div>

        </div>

        <!-- Boutons haut droite -->
        <div class="absolute top-6 right-6 flex gap-3 z-20">
            <RouterLink
                :to="{ name: 'register' }"
                class="px-5 py-2.5 rounded-2xl border-2 border-theme font-dynapuff font-bold text-sm text-theme hover:bg-black/10 transition-colors"
            >
                {{ t('pages.landing.register') }}
            </RouterLink>
            <RouterLink
                :to="{ name: 'login' }"
                class="btn-theme px-5 py-2.5 rounded-2xl font-dynapuff font-bold text-sm text-white"
            >
                {{ t('pages.landing.login') }}
            </RouterLink>
        </div>

        <!-- Titre centré -->
        <div class="relative z-10 min-h-screen flex items-center justify-center">
            <h1 class="font-dynapuff font-bold text-6xl md:text-8xl text-theme text-center drop-shadow-sm px-8">
                {{ t('app_name') }}
            </h1>
        </div>

    </div>
</template>

<style scoped>
@keyframes card-float {
    0%, 100% { transform: rotate(var(--r)) translateY(0); }
    50%       { transform: rotate(var(--r)) translateY(var(--dy)); }
}

.card-anim {
    animation: card-float var(--dur) ease-in-out infinite var(--delay);
}
</style>
