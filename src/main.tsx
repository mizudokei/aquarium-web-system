import React from "react";
import ReactDOM from "react-dom/client";
import { Calendar } from "./Calendar/Calendar";
import { ChakraProvider } from "@chakra-ui/react";

ReactDOM.createRoot(document.getElementById("root") as HTMLElement).render(
  <React.StrictMode>
    <ChakraProvider>
      <Calendar />
    </ChakraProvider>
  </React.StrictMode>
);
