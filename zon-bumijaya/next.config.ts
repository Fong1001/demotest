import type { NextConfig } from "next";
import createNextIntlPlugin from "next-intl/plugin";
import path from "path";

const withNextIntl = createNextIntlPlugin("./src/i18n/request.ts");

// output:"export" is required for Cloudflare Pages static hosting but
// is incompatible with middleware (i18n proxy). Only enable it during
// production builds (e.g. `npm run build`), not local dev.
const isStaticExport = process.env.NODE_ENV === "production";

const nextConfig: NextConfig = {
  ...(isStaticExport && { output: "export" }),
  trailingSlash: true,

  turbopack: {
    root: path.resolve(__dirname),
  },

  images: {
    // Required because Next.js image optimization doesn't run on static hosts
    unoptimized: true,

    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },
      {
        protocol: "https",
        hostname: "placehold.co",
      },
    ],
  },
};

export default withNextIntl(nextConfig);
