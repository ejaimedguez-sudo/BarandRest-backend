import { useEffect, useMemo, useState } from "react";
import { API_BASE, endpoints } from "./api";

const modules = [
  "Menu QR",
  "Mesas y Meseros",
  "Ordenes",
  "Recetas y Costos",
  "Inventarios",
  "Pagos y Facturacion",
  "BI Gerencial"
];

function Login({ onLogin }) {
  const [email, setEmail] = useState("admin@barandrest.local");
  const [password, setPassword] = useState("Demo12345");
  const [error, setError] = useState("");

  const submit = async (e) => {
    e.preventDefault();
    try {
      const data = await endpoints.login({ email, password });
      localStorage.setItem("barandrest_token", data.token);
      localStorage.setItem("barandrest_user", JSON.stringify(data.user));
      setError("");
      onLogin(data.user);
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <form className="card login" onSubmit={submit}>
      <h2>Acceso Plataforma</h2>
      <input value={email} onChange={(e) => setEmail(e.target.value)} placeholder="email" />
      <input value={password} onChange={(e) => setPassword(e.target.value)} type="password" placeholder="password" />
      <button type="submit">Entrar</button>
      {error && <p className="error">{error}</p>}
    </form>
  );
}

function Dashboard() {
  const [sales, setSales] = useState(null);
  const from = useMemo(() => new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10), []);
  const to = useMemo(() => new Date().toISOString().slice(0, 10), []);

  useEffect(() => {
    endpoints.sales(from, to).then(setSales).catch(() => setSales(null));
  }, [from, to]);

  return (
    <section className="card">
      <h2>Dashboard Gerencial</h2>
      {sales ? (
        <div className="grid">
          <div><strong>Ventas actuales:</strong> ${Number(sales.sales.current).toFixed(2)}</div>
          <div><strong>Ventas periodo previo:</strong> ${Number(sales.sales.previous).toFixed(2)}</div>
          <div><strong>Crecimiento:</strong> {Number(sales.sales.growthPct).toFixed(2)}%</div>
          <div><strong>Ordenes:</strong> {sales.kpis.orders}</div>
          <div><strong>Ticket promedio:</strong> ${Number(sales.kpis.averageTicket).toFixed(2)}</div>
          <div><strong>Mesas ocupadas:</strong> {sales.kpis.occupiedTables}</div>
        </div>
      ) : (
        <p>No hay datos o falta autenticacion.</p>
      )}
    </section>
  );
}

function RealtimeFeed({ user }) {
  const [events, setEvents] = useState([]);

  useEffect(() => {
    if (!user) return;
    const token = localStorage.getItem("barandrest_token");
    if (!token) return;

    const controller = new AbortController();
    let cancelled = false;

    fetch(`${API_BASE}/realtime/events`, {
      headers: { Authorization: `Bearer ${token}` },
      signal: controller.signal
    })
      .then(async (res) => {
        if (!res.body) return;
        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = "";

        while (true) {
          const { done, value } = await reader.read();
          if (done || cancelled) break;
          buffer += decoder.decode(value, { stream: true });

          const chunks = buffer.split("\n\n");
          buffer = chunks.pop() || "";

          for (const chunk of chunks) {
            const dataLine = chunk
              .split("\n")
              .find((line) => line.startsWith("data: "));
            if (!dataLine) continue;

            try {
              const payload = JSON.parse(dataLine.slice(6));
              setEvents((prev) => [payload, ...prev].slice(0, 12));
            } catch {
              // Ignore malformed event payloads.
            }
          }
        }
      })
      .catch(() => {});

    return () => {
      cancelled = true;
      controller.abort();
    };
  }, [user]);

  return (
    <section className="card">
      <h2>Realtime Operacion</h2>
      {events.length === 0 ? (
        <p>Esperando eventos...</p>
      ) : (
        <div className="events">
          {events.map((evt, idx) => (
            <div className="event" key={`${evt.type}-${evt.occurredAt}-${idx}`}>
              <strong>{evt.type}</strong>
              <span>{new Date(evt.occurredAt).toLocaleString()}</span>
            </div>
          ))}
        </div>
      )}
    </section>
  );
}

export default function App() {
  const [user, setUser] = useState(() => {
    const raw = localStorage.getItem("barandrest_user");
    return raw ? JSON.parse(raw) : null;
  });

  return (
    <main>
      <header className="hero">
        <h1>BarAndRest Admin Web</h1>
        <p>Administracion integral para restaurant-bar: operacion, inventario, costos, pagos y BI.</p>
      </header>

      {!user ? <Login onLogin={setUser} /> : null}

      <section className="card">
        <h2>Modulos</h2>
        <div className="chips">
          {modules.map((m) => <span key={m}>{m}</span>)}
        </div>
      </section>

      <Dashboard />
      <RealtimeFeed user={user} />
    </main>
  );
}
