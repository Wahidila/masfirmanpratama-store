/**
 * Alpine global store: cart
 * --------------------------------------------------------------------------
 * MVP frontend cart, persisted to localStorage. Backend session cart akan
 * menggantikan persistence layer di M2 (controller-driven). API store
 * diusahakan stabil supaya migration nanti minim breaking change.
 *
 * Schema item:
 *   { slug: string, name: string, price: number, image?: string,
 *     qty: number, category?: string }
 *
 * Public surface (gunakan dari Blade lewat $store.cart):
 *   - items                  -> array item
 *   - count                  -> total qty (badge navbar)
 *   - subtotal               -> sum(item.price * item.qty)
 *   - shipping               -> placeholder ongkir (M2: dari Agenwebsite API)
 *   - total                  -> subtotal + shipping
 *   - isEmpty                -> count === 0
 *   - add(item, qty?)        -> tambah / merge by slug
 *   - update(slug, qty)      -> set qty absolut (qty <= 0 => remove)
 *   - increment(slug)        -> +1
 *   - decrement(slug)        -> -1 (otomatis remove di 0)
 *   - remove(slug)           -> hapus
 *   - clear()                -> kosongkan
 *   - format(amount)         -> "Rp 4.500.000"
 *
 * Persistence:
 *   - localStorage key 'mfp_cart_v1'
 *   - 'storage' event listener untuk sinkron antar-tab
 *   - 'cart:changed' CustomEvent dispatched ke window setiap mutasi
 */

const STORAGE_KEY = 'mfp_cart_v1';

function loadFromStorage() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) return [];
        return parsed
            .filter((it) => it && typeof it.slug === 'string' && it.slug.length > 0)
            .map((it) => ({
                slug: String(it.slug),
                name: String(it.name ?? ''),
                price: Number(it.price) || 0,
                image: it.image ? String(it.image) : null,
                category: it.category ? String(it.category) : null,
                qty: Math.max(1, Math.floor(Number(it.qty) || 1)),
                is_shippable: it.is_shippable !== false, // default true; false only for digital items
            }));
    } catch (e) {
        // localStorage unavailable / disabled / corrupted JSON — start clean.
        // eslint-disable-next-line no-console
        console.warn('[cart] failed to load from storage:', e);
        return [];
    }
}

function saveToStorage(items) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch (e) {
        // Quota exceeded / private mode — fail soft.
        // eslint-disable-next-line no-console
        console.warn('[cart] failed to save to storage:', e);
    }
}

const cart = {
    items: [],
    shipping: 0,

    init() {
        this.items = loadFromStorage();

        // Cross-tab sync: when another tab mutates the cart, mirror it here.
        if (typeof window !== 'undefined') {
            window.addEventListener('storage', (event) => {
                if (event.key !== STORAGE_KEY) return;
                this.items = loadFromStorage();
                this._notify({ source: 'storage' });
            });
        }
    },

    // ── Computed ────────────────────────────────────────────────────────────

    get count() {
        return this.items.reduce((sum, it) => sum + (Number(it.qty) || 0), 0);
    },

    get subtotal() {
        return this.items.reduce(
            (sum, it) => sum + (Number(it.price) || 0) * (Number(it.qty) || 0),
            0,
        );
    },

    get total() {
        return this.subtotal + (Number(this.shipping) || 0);
    },

    get isEmpty() {
        return this.items.length === 0;
    },

    // ── Mutations ───────────────────────────────────────────────────────────

    add(item, qty = 1) {
        if (!item || typeof item.slug !== 'string' || item.slug.length === 0) {
            return;
        }
        // Kelas tidak boleh masuk cart — harus lewat checkout kelas
        if (item.type === 'kelas' || item.type === 'course') {
            return;
        }
        const addQty = Math.max(1, Math.floor(Number(qty) || 1));
        const existing = this.items.find((it) => it.slug === item.slug);
        if (existing) {
            existing.qty += addQty;
        } else {
            this.items.push({
                slug: String(item.slug),
                name: String(item.name ?? ''),
                price: Number(item.price) || 0,
                image: item.image ? String(item.image) : null,
                category: item.category ? String(item.category) : null,
                qty: addQty,
                is_shippable: item.is_shippable !== false, // default true; false for digital
            });
        }
        this._persist();
    },

    update(slug, qty) {
        const next = Math.floor(Number(qty) || 0);
        if (next <= 0) {
            this.remove(slug);
            return;
        }
        const item = this.items.find((it) => it.slug === slug);
        if (item) {
            item.qty = next;
            this._persist();
        }
    },

    increment(slug) {
        const item = this.items.find((it) => it.slug === slug);
        if (item) {
            item.qty += 1;
            this._persist();
        }
    },

    decrement(slug) {
        const item = this.items.find((it) => it.slug === slug);
        if (!item) return;
        if (item.qty <= 1) {
            this.remove(slug);
            return;
        }
        item.qty -= 1;
        this._persist();
    },

    remove(slug) {
        const before = this.items.length;
        this.items = this.items.filter((it) => it.slug !== slug);
        if (this.items.length !== before) {
            this._persist();
        }
    },

    clear() {
        if (this.items.length === 0) return;
        this.items = [];
        this._persist();
    },

    // ── Helpers ─────────────────────────────────────────────────────────────

    format(amount) {
        const n = Number(amount) || 0;
        return 'Rp ' + n.toLocaleString('id-ID');
    },

    // ── Internals ───────────────────────────────────────────────────────────

    _persist() {
        saveToStorage(this.items);
        this._notify({ source: 'local' });
    },

    _notify(detail = {}) {
        if (typeof window === 'undefined') return;
        window.dispatchEvent(new CustomEvent('cart:changed', { detail }));
    },
};

export default cart;
