import React, { useState } from "react";
import { SafeAreaView, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";
import { StatusBar } from "expo-status-bar";

const API_BASE = "http://localhost:4100/api";

export default function App() {
  const [tableId, setTableId] = useState("1");
  const [menuResult, setMenuResult] = useState("");

  const loadMenu = async () => {
    try {
      const res = await fetch(`${API_BASE}/menu/public`);
      const data = await res.json();
      setMenuResult(`Menu activo: ${data.length} elementos`);
    } catch {
      setMenuResult("No se pudo conectar con API");
    }
  };

  const sendGuestOrder = async (useWaiter) => {
    try {
      const payload = {
        tableId: Number(tableId),
        useWaiter,
        items: [{ menuItemId: 1, qty: 1, unitPrice: 120 }]
      };
      const res = await fetch(`${API_BASE}/ops/orders/guest`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      setMenuResult(`Comanda creada #${data.orderId}`);
    } catch {
      setMenuResult("No se pudo enviar comanda");
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <StatusBar style="dark" />
      <ScrollView contentContainerStyle={styles.container}>
        <Text style={styles.title}>BarAndRest Mobile</Text>
        <Text style={styles.subtitle}>Comensal, mesero y cajero en un solo flujo movil</Text>

        <View style={styles.card}>
          <Text style={styles.label}>Mesa</Text>
          <TextInput style={styles.input} value={tableId} onChangeText={setTableId} keyboardType="numeric" />

          <TouchableOpacity style={styles.btnPrimary} onPress={loadMenu}>
            <Text style={styles.btnText}>Ver menu digital</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.btnSecondary} onPress={() => sendGuestOrder(false)}>
            <Text style={styles.btnText}>Pedir desde dispositivo</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.btnDark} onPress={() => sendGuestOrder(true)}>
            <Text style={styles.btnText}>Llamar mesero para comanda</Text>
          </TouchableOpacity>

          <Text style={styles.result}>{menuResult}</Text>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: "#efe5d8" },
  container: { padding: 20, gap: 14 },
  title: { fontSize: 30, fontWeight: "800", color: "#3d2514" },
  subtitle: { fontSize: 15, color: "#64442f" },
  card: { backgroundColor: "#fffaf3", borderRadius: 16, padding: 16, gap: 10 },
  label: { fontWeight: "700", color: "#3d2514" },
  input: { backgroundColor: "#fff", borderColor: "#d6c6b0", borderWidth: 1, borderRadius: 10, padding: 10 },
  btnPrimary: { backgroundColor: "#a84a23", borderRadius: 12, padding: 12 },
  btnSecondary: { backgroundColor: "#0f766e", borderRadius: 12, padding: 12 },
  btnDark: { backgroundColor: "#26211d", borderRadius: 12, padding: 12 },
  btnText: { color: "white", fontWeight: "700", textAlign: "center" },
  result: { marginTop: 10, color: "#3d2514" }
});
