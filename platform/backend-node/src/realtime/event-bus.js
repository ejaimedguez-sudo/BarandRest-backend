import { EventEmitter } from "node:events";

const eventBus = new EventEmitter();
eventBus.setMaxListeners(100);

export function publishEvent(type, payload) {
  eventBus.emit("event", {
    type,
    payload,
    occurredAt: new Date().toISOString()
  });
}

export function subscribeEvents(listener) {
  eventBus.on("event", listener);
  return () => eventBus.off("event", listener);
}
