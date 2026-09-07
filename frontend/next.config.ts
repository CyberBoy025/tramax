import type { NextConfig } from "next";
import path from "node:path";

const nextConfig: NextConfig = {
  // Pin the workspace root: an unrelated package-lock.json sits further up
  // this machine's directory tree (outside this project's own git repo),
  // which otherwise makes Next.js guess the wrong root.
  turbopack: {
    root: path.join(__dirname),
  },
};

export default nextConfig;
