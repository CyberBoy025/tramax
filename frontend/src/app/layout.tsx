import type { Metadata } from "next";
import { Outfit, Public_Sans, Newsreader, IBM_Plex_Mono } from "next/font/google";
import "./globals.css";

// Font roles per /ui.md §4 Typography Tokens.
// Outfit stands in for the "rounded geometric sans" Display/Heading class.
// Public Sans and IBM Plex Mono are named directly in ui.md.
// Newsreader Italic stands in for the "Accent Serif" single-word emphasis device.
const fontDisplay = Outfit({
  variable: "--font-display",
  subsets: ["latin"],
  weight: ["500", "600", "700"],
});

const fontBody = Public_Sans({
  variable: "--font-body",
  subsets: ["latin"],
  weight: ["400", "500", "600"],
});

const fontAccent = Newsreader({
  variable: "--font-accent",
  subsets: ["latin"],
  style: ["italic"],
  weight: ["500"],
});

const fontData = IBM_Plex_Mono({
  variable: "--font-data",
  subsets: ["latin"],
  weight: ["400", "500", "600"],
});

export const metadata: Metadata = {
  title: {
    default: "Tramax Entertainment",
    template: "%s · Tramax Entertainment",
  },
  description: "Discover. Develop. Promote.",
};

export default function RootLayout({ children }: LayoutProps<"/">) {
  return (
    <html
      lang="en"
      className={`${fontDisplay.variable} ${fontBody.variable} ${fontAccent.variable} ${fontData.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col">{children}</body>
    </html>
  );
}
