import { Router } from "express";
import { subscribeEvents } from "../realtime/event-bus.js";
import { authRequired } from "../middlewares/auth.js";

const router = Router();

router.get("/events", authRequired, (req, res) => {
  res.setHeader("Content-Type", "text/event-stream");
  res.setHeader("Cache-Control", "no-cache");
  res.setHeader("Connection", "keep-alive");
  res.flushHeaders?.();

  const send = (event) => {
    res.write(`event: ${event.type}\n`);
    res.write(`data: ${JSON.stringify(event)}\n\n`);
  };

  send({ type: "connected", payload: { userId: req.user.id }, occurredAt: new Date().toISOString() });
  const unsubscribe = subscribeEvents(send);

  const heartbeat = setInterval(() => {
    res.write("event: heartbeat\n");
    res.write(`data: ${JSON.stringify({ ts: new Date().toISOString() })}\n\n`);
  }, 20000);

  req.on("close", () => {
    clearInterval(heartbeat);
    unsubscribe();
    res.end();
  });
});

export default router;
